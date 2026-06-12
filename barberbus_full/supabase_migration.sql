-- =============================================================
-- BARBERBUS – SUPABASE MIGRATION
-- Run this in Supabase SQL Editor (Dashboard → SQL Editor → New Query)
-- =============================================================

-- 1. Add auth_id column to users table to link with Supabase Auth
ALTER TABLE public.users ADD COLUMN IF NOT EXISTS auth_id UUID REFERENCES auth.users(id) ON DELETE SET NULL;
ALTER TABLE public.users ADD COLUMN IF NOT EXISTS is_walkin BOOLEAN DEFAULT FALSE;

-- 2. Add is_walkin to bookings
ALTER TABLE public.bookings ADD COLUMN IF NOT EXISTS is_walkin BOOLEAN DEFAULT FALSE;

-- 3. Update role enum to include 'officer'
-- (If using Supabase, recreate the column since ALTER ENUM is limited)
ALTER TABLE public.users ALTER COLUMN role TYPE TEXT;
-- Valid values: 'user', 'admin', 'officer'

-- 4. Enable Row Level Security (RLS) on all tables
ALTER TABLE public.users        ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.barbers      ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.services     ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.bookings     ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.reviews      ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.promotions   ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.store_settings ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.page_content ENABLE ROW LEVEL SECURITY;

-- 5. Create helper function to get current user's role
CREATE OR REPLACE FUNCTION public.get_user_role()
RETURNS TEXT AS $$
  SELECT role FROM public.users WHERE auth_id = auth.uid() LIMIT 1;
$$ LANGUAGE SQL SECURITY DEFINER;

-- 6. RLS Policies

-- USERS table
DROP POLICY IF EXISTS "users_read_own"    ON public.users;
DROP POLICY IF EXISTS "users_staff_all"   ON public.users;

CREATE POLICY "users_read_own" ON public.users
  FOR SELECT USING (auth_id = auth.uid());

CREATE POLICY "users_staff_all" ON public.users
  FOR ALL USING (get_user_role() IN ('admin', 'officer'));

-- BARBERS – public read, staff write
DROP POLICY IF EXISTS "barbers_public_read" ON public.barbers;
DROP POLICY IF EXISTS "barbers_admin_write" ON public.barbers;

CREATE POLICY "barbers_public_read" ON public.barbers
  FOR SELECT USING (true);

CREATE POLICY "barbers_admin_write" ON public.barbers
  FOR ALL USING (get_user_role() = 'admin');

-- SERVICES – public read, admin write
DROP POLICY IF EXISTS "services_public_read" ON public.services;
DROP POLICY IF EXISTS "services_admin_write" ON public.services;

CREATE POLICY "services_public_read" ON public.services
  FOR SELECT USING (true);

CREATE POLICY "services_admin_write" ON public.services
  FOR ALL USING (get_user_role() = 'admin');

-- BOOKINGS – users see own, staff see all
DROP POLICY IF EXISTS "bookings_own"       ON public.bookings;
DROP POLICY IF EXISTS "bookings_staff_all" ON public.bookings;

CREATE POLICY "bookings_own" ON public.bookings
  FOR SELECT USING (
    user_id IN (SELECT id FROM public.users WHERE auth_id = auth.uid())
  );

CREATE POLICY "bookings_staff_all" ON public.bookings
  FOR ALL USING (get_user_role() IN ('admin', 'officer'));

-- PROMOTIONS – public read, admin write
DROP POLICY IF EXISTS "promos_public_read" ON public.promotions;
DROP POLICY IF EXISTS "promos_admin_write" ON public.promotions;

CREATE POLICY "promos_public_read" ON public.promotions
  FOR SELECT USING (true);

CREATE POLICY "promos_admin_write" ON public.promotions
  FOR ALL USING (get_user_role() = 'admin');

-- STORE SETTINGS – public read, admin write
DROP POLICY IF EXISTS "store_public_read" ON public.store_settings;
DROP POLICY IF EXISTS "store_admin_write" ON public.store_settings;

CREATE POLICY "store_public_read" ON public.store_settings
  FOR SELECT USING (true);

CREATE POLICY "store_admin_write" ON public.store_settings
  FOR ALL USING (get_user_role() = 'admin');

-- PAGE CONTENT
DROP POLICY IF EXISTS "pages_public_read" ON public.page_content;
DROP POLICY IF EXISTS "pages_admin_write" ON public.page_content;

CREATE POLICY "pages_public_read" ON public.page_content
  FOR SELECT USING (true);

CREATE POLICY "pages_admin_write" ON public.page_content
  FOR ALL USING (get_user_role() = 'admin');

-- 7. Function to auto-create user record on signup
CREATE OR REPLACE FUNCTION public.handle_new_user()
RETURNS TRIGGER AS $$
BEGIN
  INSERT INTO public.users (auth_id, name, email, phone, role)
  VALUES (
    NEW.id,
    COALESCE(NEW.raw_user_meta_data->>'name', split_part(NEW.email, '@', 1)),
    NEW.email,
    COALESCE(NEW.raw_user_meta_data->>'phone', ''),
    COALESCE(NEW.raw_user_meta_data->>'role', 'user')
  );
  RETURN NEW;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users;
CREATE TRIGGER on_auth_user_created
  AFTER INSERT ON auth.users
  FOR EACH ROW EXECUTE PROCEDURE public.handle_new_user();

-- 8. Create walk-in guest user (update ID in officer_walkin_screen.dart)
INSERT INTO public.users (name, email, phone, password, role)
VALUES ('Walk-in Guest', 'walkin@barberbus.internal', '', 'N/A', 'user')
ON CONFLICT DO NOTHING;

-- =============================================================
-- SETUP SUPABASE AUTH USERS (run in Supabase Dashboard → Auth → Users)
-- Or use this SQL to link existing PHP users to Supabase Auth:
-- 1. Go to Supabase Dashboard → Authentication → Users
-- 2. Click "Add User" → Enter email + password
-- 3. Copy the new auth.users UUID
-- 4. UPDATE public.users SET auth_id = '<uuid>' WHERE email = 'admin@barberbus.com';
-- =============================================================

-- 9. Enable Realtime for bookings
ALTER PUBLICATION supabase_realtime ADD TABLE public.bookings;

-- DONE ✓
