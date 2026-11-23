-- Local development data only. `supabase db push` does not run this file.

insert into public.organizations (id, name, slug, is_demo) values
  ('11111111-1111-1111-1111-111111111111', 'Jhumel', 'jhumel', false),
  ('22222222-2222-2222-2222-222222222222', 'Demo', 'demo', true);

-- GoTrue rejects logins when these token columns are null, so they are set to empty strings.
insert into auth.users (
  instance_id, id, aud, role, email, encrypted_password, email_confirmed_at,
  raw_app_meta_data, raw_user_meta_data, created_at, updated_at,
  confirmation_token, recovery_token, email_change_token_new, email_change
) values
  (
    '00000000-0000-0000-0000-000000000000', 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
    'authenticated', 'authenticated', 'jhumel@jhumel.dev',
    extensions.crypt('local-password', extensions.gen_salt('bf')), now(),
    '{"provider": "email", "providers": ["email"]}', '{}', now(), now(),
    '', '', '', ''
  ),
  (
    '00000000-0000-0000-0000-000000000000', 'dddddddd-dddd-dddd-dddd-dddddddddddd',
    'authenticated', 'authenticated', 'demo@jhumel.dev',
    extensions.crypt('demo-password', extensions.gen_salt('bf')), now(),
    '{"provider": "email", "providers": ["email"]}', '{}', now(), now(),
    '', '', '', ''
  );

insert into auth.identities (provider_id, user_id, identity_data, provider, last_sign_in_at, created_at, updated_at) values
  (
    'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
    '{"sub": "aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa", "email": "jhumel@jhumel.dev", "email_verified": true}',
    'email', now(), now(), now()
  ),
  (
    'dddddddd-dddd-dddd-dddd-dddddddddddd', 'dddddddd-dddd-dddd-dddd-dddddddddddd',
    '{"sub": "dddddddd-dddd-dddd-dddd-dddddddddddd", "email": "demo@jhumel.dev", "email_verified": true}',
    'email', now(), now(), now()
  );

insert into public.organization_members (organization_id, user_id, role) values
  ('11111111-1111-1111-1111-111111111111', 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', 'owner'),
  ('22222222-2222-2222-2222-222222222222', 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', 'owner'),
  ('22222222-2222-2222-2222-222222222222', 'dddddddd-dddd-dddd-dddd-dddddddddddd', 'member');

-- The demo organization's sample leads come from the same function that resets them every night.
select public.reset_demo_data();
