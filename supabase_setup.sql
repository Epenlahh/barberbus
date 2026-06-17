-- =============================================================
-- BARBERBUS – SUPABASE SETUP (PostgreSQL)
-- Run this entire file in: Supabase Dashboard → SQL Editor → New Query
-- =============================================================

-- ── EXTENSIONS ──
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- ── updated_at trigger function (replaces MySQL ON UPDATE) ──
CREATE OR REPLACE FUNCTION public.set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = NOW();
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- =============================================================
-- TABLES
-- =============================================================

-- ── USERS ──
CREATE TABLE IF NOT EXISTS public.users (
    id          BIGSERIAL PRIMARY KEY,
    auth_id     UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    name        VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    phone       VARCHAR(20),
    password    VARCHAR(255)  NOT NULL DEFAULT 'N/A',
    role        TEXT          NOT NULL DEFAULT 'user'
                    CHECK (role IN ('user', 'admin', 'officer')),
    avatar      VARCHAR(255),
    is_walkin   BOOLEAN       NOT NULL DEFAULT FALSE,
    created_at  TIMESTAMPTZ   NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMPTZ   NOT NULL DEFAULT NOW()
);

DROP TRIGGER IF EXISTS users_set_updated_at ON public.users;
CREATE TRIGGER users_set_updated_at
  BEFORE UPDATE ON public.users
  FOR EACH ROW EXECUTE FUNCTION public.set_updated_at();

-- ── BARBERS ──
CREATE TABLE IF NOT EXISTS public.barbers (
    id          BIGSERIAL PRIMARY KEY,
    name        VARCHAR(100)  NOT NULL,
    specialty   VARCHAR(150),
    bio         TEXT,
    experience  INT           NOT NULL DEFAULT 0,
    rating      NUMERIC(2,1)  NOT NULL DEFAULT 5.0,
    photo       VARCHAR(255),
    is_active   BOOLEAN       NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMPTZ   NOT NULL DEFAULT NOW()
);

-- ── SERVICES ──
CREATE TABLE IF NOT EXISTS public.services (
    id          BIGSERIAL PRIMARY KEY,
    name        VARCHAR(100)  NOT NULL,
    description TEXT,
    price       NUMERIC(8,2)  NOT NULL,
    duration    INT           NOT NULL,  -- minutes
    category    VARCHAR(50)   NOT NULL DEFAULT 'haircut',
    is_active   BOOLEAN       NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMPTZ   NOT NULL DEFAULT NOW()
);

-- ── BOOKINGS ──
CREATE TABLE IF NOT EXISTS public.bookings (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT        NOT NULL REFERENCES public.users(id)    ON DELETE CASCADE,
    barber_id       BIGINT                 REFERENCES public.barbers(id)  ON DELETE SET NULL,
    service_id      BIGINT        NOT NULL REFERENCES public.services(id) ON DELETE RESTRICT,
    booking_date    DATE          NOT NULL,
    booking_time    TIME          NOT NULL,
    status          TEXT          NOT NULL DEFAULT 'pending'
                        CHECK (status IN ('pending','confirmed','completed','cancelled')),
    total_price     NUMERIC(8,2)  NOT NULL,
    pay_method      VARCHAR(50)   NOT NULL DEFAULT 'cash',
    notes           TEXT,
    is_walkin       BOOLEAN       NOT NULL DEFAULT FALSE,
    created_at      TIMESTAMPTZ   NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ   NOT NULL DEFAULT NOW()
);

DROP TRIGGER IF EXISTS bookings_set_updated_at ON public.bookings;
CREATE TRIGGER bookings_set_updated_at
  BEFORE UPDATE ON public.bookings
  FOR EACH ROW EXECUTE FUNCTION public.set_updated_at();

-- ── REVIEWS ──
CREATE TABLE IF NOT EXISTS public.reviews (
    id          BIGSERIAL PRIMARY KEY,
    user_id     BIGINT  NOT NULL REFERENCES public.users(id)    ON DELETE CASCADE,
    booking_id  BIGINT  NOT NULL REFERENCES public.bookings(id) ON DELETE CASCADE,
    barber_id   BIGINT           REFERENCES public.barbers(id)  ON DELETE SET NULL,
    rating      SMALLINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment     TEXT,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- ── STORE SETTINGS ──
CREATE TABLE IF NOT EXISTS public.store_settings (
    id              BIGSERIAL PRIMARY KEY,
    name            VARCHAR(150)  NOT NULL DEFAULT 'BarberBus',
    description     TEXT,
    is_open         BOOLEAN       NOT NULL DEFAULT TRUE,
    profile_image   VARCHAR(255),
    business_hours  VARCHAR(255),
    location        VARCHAR(255),
    phone           VARCHAR(20),
    email           VARCHAR(150),
    website         VARCHAR(255),
    updated_by      BIGINT REFERENCES public.users(id) ON DELETE SET NULL,
    created_at      TIMESTAMPTZ   NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ   NOT NULL DEFAULT NOW()
);

DROP TRIGGER IF EXISTS store_set_updated_at ON public.store_settings;
CREATE TRIGGER store_set_updated_at
  BEFORE UPDATE ON public.store_settings
  FOR EACH ROW EXECUTE FUNCTION public.set_updated_at();

-- ── PAGE CONTENT ──
CREATE TABLE IF NOT EXISTS public.page_content (
    id          BIGSERIAL PRIMARY KEY,
    page_name   VARCHAR(50)   NOT NULL UNIQUE,
    title       VARCHAR(255),
    hero        TEXT,
    content     TEXT,
    footer      VARCHAR(255),
    updated_by  BIGINT REFERENCES public.users(id) ON DELETE SET NULL,
    created_at  TIMESTAMPTZ   NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMPTZ   NOT NULL DEFAULT NOW()
);

DROP TRIGGER IF EXISTS pages_set_updated_at ON public.page_content;
CREATE TRIGGER pages_set_updated_at
  BEFORE UPDATE ON public.page_content
  FOR EACH ROW EXECUTE FUNCTION public.set_updated_at();

-- ── PROMOTIONS ──
CREATE TABLE IF NOT EXISTS public.promotions (
    id          BIGSERIAL PRIMARY KEY,
    title       VARCHAR(200)  NOT NULL,
    description TEXT,
    image       VARCHAR(255),
    link        VARCHAR(255),
    is_active   BOOLEAN       NOT NULL DEFAULT TRUE,
    start_date  DATE,
    end_date    DATE,
    created_by  BIGINT REFERENCES public.users(id) ON DELETE SET NULL,
    created_at  TIMESTAMPTZ   NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMPTZ   NOT NULL DEFAULT NOW()
);

DROP TRIGGER IF EXISTS promos_set_updated_at ON public.promotions;
CREATE TRIGGER promos_set_updated_at
  BEFORE UPDATE ON public.promotions
  FOR EACH ROW EXECUTE FUNCTION public.set_updated_at();

-- =============================================================
-- ROW LEVEL SECURITY
-- =============================================================

ALTER TABLE public.users          ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.barbers        ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.services       ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.bookings       ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.reviews        ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.promotions     ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.store_settings ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.page_content   ENABLE ROW LEVEL SECURITY;

-- Helper: get current user's role
CREATE OR REPLACE FUNCTION public.get_user_role()
RETURNS TEXT AS $$
  SELECT role FROM public.users WHERE auth_id = auth.uid() LIMIT 1;
$$ LANGUAGE SQL SECURITY DEFINER STABLE;

-- USERS
DROP POLICY IF EXISTS "users_read_own"  ON public.users;
DROP POLICY IF EXISTS "users_staff_all" ON public.users;
CREATE POLICY "users_read_own"  ON public.users FOR SELECT USING (auth_id = auth.uid());
CREATE POLICY "users_staff_all" ON public.users FOR ALL    USING (get_user_role() IN ('admin','officer'));

-- BARBERS (public read, admin write)
DROP POLICY IF EXISTS "barbers_public_read" ON public.barbers;
DROP POLICY IF EXISTS "barbers_admin_write" ON public.barbers;
CREATE POLICY "barbers_public_read" ON public.barbers FOR SELECT USING (true);
CREATE POLICY "barbers_admin_write" ON public.barbers FOR ALL    USING (get_user_role() = 'admin');

-- SERVICES (public read, admin write)
DROP POLICY IF EXISTS "services_public_read" ON public.services;
DROP POLICY IF EXISTS "services_admin_write" ON public.services;
CREATE POLICY "services_public_read" ON public.services FOR SELECT USING (true);
CREATE POLICY "services_admin_write" ON public.services FOR ALL    USING (get_user_role() = 'admin');

-- BOOKINGS (user sees own, staff sees all)
DROP POLICY IF EXISTS "bookings_own"       ON public.bookings;
DROP POLICY IF EXISTS "bookings_staff_all" ON public.bookings;
CREATE POLICY "bookings_own" ON public.bookings
  FOR SELECT USING (user_id IN (SELECT id FROM public.users WHERE auth_id = auth.uid()));
CREATE POLICY "bookings_staff_all" ON public.bookings
  FOR ALL USING (get_user_role() IN ('admin','officer'));

-- REVIEWS (public read, owner write)
DROP POLICY IF EXISTS "reviews_public_read" ON public.reviews;
DROP POLICY IF EXISTS "reviews_own_write"   ON public.reviews;
CREATE POLICY "reviews_public_read" ON public.reviews FOR SELECT USING (true);
CREATE POLICY "reviews_own_write"   ON public.reviews FOR INSERT WITH CHECK (
  user_id IN (SELECT id FROM public.users WHERE auth_id = auth.uid())
);

-- PROMOTIONS (public read, admin write)
DROP POLICY IF EXISTS "promos_public_read" ON public.promotions;
DROP POLICY IF EXISTS "promos_admin_write" ON public.promotions;
CREATE POLICY "promos_public_read" ON public.promotions FOR SELECT USING (true);
CREATE POLICY "promos_admin_write" ON public.promotions FOR ALL    USING (get_user_role() = 'admin');

-- STORE SETTINGS (public read, admin write)
DROP POLICY IF EXISTS "store_public_read" ON public.store_settings;
DROP POLICY IF EXISTS "store_admin_write" ON public.store_settings;
CREATE POLICY "store_public_read" ON public.store_settings FOR SELECT USING (true);
CREATE POLICY "store_admin_write" ON public.store_settings FOR ALL    USING (get_user_role() = 'admin');

-- PAGE CONTENT (public read, admin write)
DROP POLICY IF EXISTS "pages_public_read" ON public.page_content;
DROP POLICY IF EXISTS "pages_admin_write" ON public.page_content;
CREATE POLICY "pages_public_read" ON public.page_content FOR SELECT USING (true);
CREATE POLICY "pages_admin_write" ON public.page_content FOR ALL    USING (get_user_role() = 'admin');

-- =============================================================
-- SUPABASE AUTH TRIGGER
-- Auto-creates a public.users row when someone signs up via Supabase Auth
-- =============================================================

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
  )
  ON CONFLICT (email) DO UPDATE
    SET auth_id = EXCLUDED.auth_id;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users;
CREATE TRIGGER on_auth_user_created
  AFTER INSERT ON auth.users
  FOR EACH ROW EXECUTE FUNCTION public.handle_new_user();

-- =============================================================
-- REALTIME
-- =============================================================
ALTER PUBLICATION supabase_realtime ADD TABLE public.bookings;

-- =============================================================
-- SEED DATA
-- =============================================================

-- Admin user (password: admin123)
INSERT INTO public.users (name, email, phone, password, role)
VALUES ('Admin BarberBus', 'admin@barberbus.com', '+60123456789',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON CONFLICT (email) DO NOTHING;

-- Officer user (password: officer123)
INSERT INTO public.users (name, email, phone, password, role)
VALUES ('Aariz Hakim (Officer)', 'officer@barberbus.com', '+60198765432',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'officer')
ON CONFLICT (email) DO NOTHING;

-- Walk-in guest
INSERT INTO public.users (name, email, phone, password, role, is_walkin)
VALUES ('Walk-in Guest', 'walkin@barberbus.internal', '', 'N/A', 'user', TRUE)
ON CONFLICT (email) DO NOTHING;

-- Barbers
INSERT INTO public.barbers (name, specialty, bio, experience, rating) VALUES
('Aariz Hakim',   'Fade & Taper Specialist',   'Master of precision fades with 8 years of experience. Trained in Kuala Lumpur and Singapore.', 8, 4.9),
('Danial Yusof',  'Classic Cuts & Beard',       'Specialises in classic barbering techniques and traditional straight-razor shaves.', 5, 4.8),
('Faiz Rahman',   'Modern Styles & Colour',     'Brings contemporary fashion cuts and hair colouring expertise to every appointment.', 4, 4.7),
('Hafizuddin',    'Curly & Textured Hair',      'Expert in managing and styling curly, wavy, and textured hair types.', 6, 4.8),
('Irfan Zaki',    'Skin Fade & Design',         'Creative barber specialising in skin fades and hair designs/patterns.', 3, 4.6);

-- Services
INSERT INTO public.services (name, description, price, duration, category) VALUES
('Classic Haircut',      'Timeless cut with scissors and comb. Includes wash and style.',   25.00,  30, 'haircut'),
('Fade & Taper',         'Precision skin or mid-fade, tailored to your head shape.',         35.00,  45, 'haircut'),
('Beard Grooming',       'Shape, trim, and style your beard to perfection.',                 20.00,  20, 'beard'),
('Hair & Beard Combo',   'Full haircut plus complete beard grooming service.',               50.00,  60, 'combo'),
('Kids Cut',             'Gentle and fun haircut for boys under 12 years old.',              18.00,  25, 'haircut'),
('Straight Razor Shave', 'Traditional hot-towel straight razor shave experience.',           30.00,  30, 'beard'),
('Hair Colour',          'Single colour treatment with premium dye products.',               80.00,  90, 'colour'),
('Highlights',           'Partial or full highlights for a natural sun-kissed look.',       120.00, 120, 'colour'),
('Scalp Treatment',      'Deep cleansing and conditioning scalp treatment.',                 40.00,  40, 'treatment'),
('Premium Wash & Style', 'Luxury shampoo, conditioning treatment, and blowdry style.',      35.00,  35, 'treatment');

-- Store settings
INSERT INTO public.store_settings (name, description, is_open, business_hours, location, phone)
VALUES ('BarberBus', 'Premium barbershop experience in Kuala Lumpur', TRUE,
        'Mon-Fri: 10:00 AM - 9:00 PM, Sat: 10:00 AM - 8:00 PM, Sun: 12:00 PM - 7:00 PM',
        '123 Jalan Merdeka, Kuala Lumpur', '+60123456789')
ON CONFLICT DO NOTHING;

-- Page content
INSERT INTO public.page_content (page_name, title, hero, content) VALUES
('dashboard', 'Dashboard',            'Welcome to BarberBus',            'Manage your barbershop and see real-time analytics.'),
('fashion',   'Fashion & Inspiration','Discover Your Style',             'Browse our collection of inspiring haircut styles.'),
('services',  'Our Services',         'Premium Barbering Services',      'We offer a wide range of professional barbering services.')
ON CONFLICT (page_name) DO NOTHING;

-- =============================================================
-- DONE ✓
-- Next steps:
--   1. Go to Supabase Dashboard → Authentication → Users → Add User
--      to create login accounts for admin@barberbus.com / officer@barberbus.com
--   2. Run:  UPDATE public.users SET auth_id = '<uuid>' WHERE email = 'admin@barberbus.com';
--      (replace <uuid> with the UUID shown in Auth → Users after creating the account)
--   3. Your PHP backend connects via: host=db.<project>.supabase.co port=5432
--      Make sure your server IP is allowed in Supabase → Settings → Database → Connection Pooling
-- =============================================================
