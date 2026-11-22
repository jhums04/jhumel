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

insert into public.leads (
  id, organization_id, external_id, full_name, email, phone, company, services, timeline,
  message, estimated_price, stage, score, score_reason, draft_subject, draft_body, created_at, updated_at
) values
  (
    '30000000-0000-0000-0000-000000000001', '22222222-2222-2222-2222-222222222222', 'demo-seed-1',
    'Maria Santos', 'maria@example.com', '+63 917 000 0001', 'Santos Dental',
    '{lead_system,automation}', 'standard',
    'We lose leads after hours. Patients fill out the contact form at night and nobody follows up until the next afternoon.',
    2400.00, 'new', 'high',
    'Clear, specific problem that a lead system solves directly. Budget fits and they are ready to start.',
    'Catching your after-hours patient inquiries',
    E'Hi Maria,\n\nThanks for reaching out. Losing patients who write in at night is exactly the kind of gap a lead system closes: every inquiry gets saved, scored, and answered within minutes, even at 2 a.m.\n\nCould we do a 20-minute call this week? I will walk you through how it would work for Santos Dental.\n\nJhumel',
    now() - interval '2 hours', now() - interval '2 hours'
  ),
  (
    '30000000-0000-0000-0000-000000000002', '22222222-2222-2222-2222-222222222222', 'demo-seed-2',
    'Daniel Reyes', 'daniel@example.com', '+63 918 000 0002', 'Reyes Logistics',
    '{automation}', 'rush',
    'Need our delivery booking form to create a row in Google Sheets and text the driver. Ideally live by next week.',
    500.00, 'new', 'medium',
    'Well-defined, small automation. Rush timeline is tight but realistic for the scope.',
    'Automating your delivery bookings',
    E'Hi Daniel,\n\nA booking that lands in your sheet and texts the driver is a clean n8n workflow, and next week is doable.\n\nCan you send me a screenshot of the current form and the sheet columns? I will confirm the plan the same day.\n\nJhumel',
    now() - interval '5 hours', now() - interval '5 hours'
  ),
  (
    '30000000-0000-0000-0000-000000000003', '22222222-2222-2222-2222-222222222222', 'demo-seed-3',
    'Aisha Khan', 'aisha@example.com', null, 'Brightpath Tutoring',
    '{supabase_app}', 'standard',
    'We schedule 40 tutors with spreadsheets. Looking for a simple web app where parents book sessions and tutors see their week.',
    1500.00, 'contacted', 'high',
    'Concrete use case, clear users, and the scope matches a React and Supabase app.',
    'A booking app for Brightpath',
    E'Hi Aisha,\n\nMoving 40 tutors off spreadsheets is a great fit for a small web app: parents book, tutors see their week, and you see everything in one place.\n\nWould Thursday or Friday work for a short call?\n\nJhumel',
    now() - interval '2 days', now() - interval '2 days'
  ),
  (
    '30000000-0000-0000-0000-000000000004', '22222222-2222-2222-2222-222222222222', 'demo-seed-4',
    'Tom Becker', 'tom@example.com', '+1 555 010 0004', 'Becker Roofing',
    '{wordpress_plugin}', 'standard',
    'Want a roof estimate calculator on our WordPress site.',
    600.00, 'contacted', 'medium',
    'Reasonable request, but the pricing rules for the calculator are not described yet.',
    'Your roof estimate calculator',
    E'Hi Tom,\n\nAn estimate calculator is a nice way to turn visitors into calls. To quote it properly I need to know how you price a roof today: by square footage, material, pitch?\n\nA rough list is enough to start.\n\nJhumel',
    now() - interval '3 days', now() - interval '3 days'
  ),
  (
    '30000000-0000-0000-0000-000000000005', '22222222-2222-2222-2222-222222222222', 'demo-seed-5',
    'Lena Fischer', 'lena@example.com', '+49 151 0000 0005', 'Fischer & Co Accounting',
    '{lead_system}', 'rush',
    'Tax season starts in three weeks. We need every website inquiry in one place with automatic replies before then.',
    2500.00, 'proposal_sent', 'high',
    'Urgent, well-scoped need with a hard deadline and budget that covers a rush build.',
    'Getting your inquiries sorted before tax season',
    E'Hi Lena,\n\nThree weeks is enough to have every inquiry captured, answered, and tracked before tax season starts. I have sent the proposal with a week-by-week plan.\n\nJhumel',
    now() - interval '5 days', now() - interval '5 days'
  ),
  (
    '30000000-0000-0000-0000-000000000006', '22222222-2222-2222-2222-222222222222', 'demo-seed-6',
    'Carlos Mendoza', 'carlos@example.com', '+63 919 000 0006', 'Mendoza Fitness',
    '{rescue}', 'standard',
    'Our membership site was built by a freelancer who disappeared. Payments fail sometimes and we do not know why.',
    300.00, 'won', 'medium',
    'Real problem with a clear first step. Small scope that could grow into ongoing work.',
    'Finding out why your payments fail',
    E'Hi Carlos,\n\nIntermittent payment failures usually come down to a few common causes. An audit will tell us which one it is and what it takes to fix.\n\nJhumel',
    now() - interval '7 days', now() - interval '7 days'
  ),
  (
    '30000000-0000-0000-0000-000000000007', '22222222-2222-2222-2222-222222222222', 'demo-seed-7',
    'Priya Nair', 'priya@example.com', null, 'Nair Interiors',
    '{supabase_app,automation}', 'standard',
    'Need a client portal for project photos and approvals, and an email to the client whenever a new photo is uploaded.',
    1900.00, 'won', 'high',
    'Clear requirements, good budget fit, and a natural combination of app and automation.',
    'A client portal for Nair Interiors',
    E'Hi Priya,\n\nA portal where clients see progress photos and approve them, with an email on every upload, is a great fit for what I build.\n\nShall we set up a kickoff call?\n\nJhumel',
    now() - interval '9 days', now() - interval '9 days'
  ),
  (
    '30000000-0000-0000-0000-000000000008', '22222222-2222-2222-2222-222222222222', 'demo-seed-8',
    'Ben Carter', 'ben@example.com', null, null,
    '{rescue}', 'standard',
    'website broken pls help',
    300.00, 'lost', 'low',
    'Vague request with no details about the site, the problem, or the business.',
    'About your website',
    E'Hi Ben,\n\nHappy to take a look. Could you send the website address and a short description of what is broken?\n\nJhumel',
    now() - interval '10 days', now() - interval '10 days'
  );

-- Follow-ups sent from the demo board are never delivered, hence delivered = false.
insert into public.lead_activities (lead_id, organization_id, type, details, created_by, created_at) values
  ('30000000-0000-0000-0000-000000000003', '22222222-2222-2222-2222-222222222222', 'email_sent',
   '{"subject": "A booking app for Brightpath", "delivered": false}', 'dddddddd-dddd-dddd-dddd-dddddddddddd',
   now() - interval '2 days' + interval '35 minutes'),
  ('30000000-0000-0000-0000-000000000004', '22222222-2222-2222-2222-222222222222', 'email_sent',
   '{"subject": "Your roof estimate calculator", "delivered": false}', 'dddddddd-dddd-dddd-dddd-dddddddddddd',
   now() - interval '3 days' + interval '2 hours'),
  ('30000000-0000-0000-0000-000000000005', '22222222-2222-2222-2222-222222222222', 'email_sent',
   '{"subject": "Getting your inquiries sorted before tax season", "delivered": false}', 'dddddddd-dddd-dddd-dddd-dddddddddddd',
   now() - interval '5 days' + interval '20 minutes'),
  ('30000000-0000-0000-0000-000000000006', '22222222-2222-2222-2222-222222222222', 'email_sent',
   '{"subject": "Finding out why your payments fail", "delivered": false}', 'dddddddd-dddd-dddd-dddd-dddddddddddd',
   now() - interval '7 days' + interval '1 hour'),
  ('30000000-0000-0000-0000-000000000007', '22222222-2222-2222-2222-222222222222', 'email_sent',
   '{"subject": "A client portal for Nair Interiors", "delivered": false}', 'dddddddd-dddd-dddd-dddd-dddddddddddd',
   now() - interval '9 days' + interval '45 minutes'),
  ('30000000-0000-0000-0000-000000000008', '22222222-2222-2222-2222-222222222222', 'email_sent',
   '{"subject": "About your website", "delivered": false}', 'dddddddd-dddd-dddd-dddd-dddddddddddd',
   now() - interval '10 days' + interval '3 hours');
