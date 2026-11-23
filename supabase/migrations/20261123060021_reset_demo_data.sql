-- Visitors try the demo board with their own test leads. Every night the demo organization goes back to the
-- same eight sample leads, so the next visitor finds a clean, realistic pipeline.

create extension if not exists pg_cron;

-- Security definer: it runs from pg_cron, and it must delete and insert regardless of row-level security.
-- search_path is empty so every name below is schema-qualified and cannot be hijacked.
create function public.reset_demo_data()
returns void
language plpgsql
security definer
set search_path = ''
as $$
declare
  demo_organization_id uuid;
begin
  select id into strict demo_organization_id from public.organizations where is_demo;

  -- Activities go with their leads (on delete cascade).
  delete from public.leads where organization_id = demo_organization_id;

  insert into public.leads (
    id, organization_id, external_id, full_name, email, phone, company, services, timeline,
    message, estimated_price, stage, score, score_reason, draft_subject, draft_body, created_at, updated_at
  ) values
    (
      '30000000-0000-0000-0000-000000000001', demo_organization_id, 'demo-seed-1',
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
      '30000000-0000-0000-0000-000000000002', demo_organization_id, 'demo-seed-2',
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
      '30000000-0000-0000-0000-000000000003', demo_organization_id, 'demo-seed-3',
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
      '30000000-0000-0000-0000-000000000004', demo_organization_id, 'demo-seed-4',
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
      '30000000-0000-0000-0000-000000000005', demo_organization_id, 'demo-seed-5',
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
      '30000000-0000-0000-0000-000000000006', demo_organization_id, 'demo-seed-6',
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
      '30000000-0000-0000-0000-000000000007', demo_organization_id, 'demo-seed-7',
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
      '30000000-0000-0000-0000-000000000008', demo_organization_id, 'demo-seed-8',
      'Ben Carter', 'ben@example.com', null, null,
      '{rescue}', 'standard',
      'website broken pls help',
      300.00, 'lost', 'low',
      'Vague request with no details about the site, the problem, or the business.',
      'About your website',
      E'Hi Ben,\n\nHappy to take a look. Could you send the website address and a short description of what is broken?\n\nJhumel',
      now() - interval '10 days', now() - interval '10 days'
    );

  -- Demo follow-ups are logged but never emailed, hence delivered = false.
  insert into public.lead_activities (lead_id, organization_id, type, details, created_by, created_at) values
    ('30000000-0000-0000-0000-000000000003', demo_organization_id, 'email_sent',
     '{"subject": "A booking app for Brightpath", "delivered": false}', null,
     now() - interval '2 days' + interval '35 minutes'),
    ('30000000-0000-0000-0000-000000000004', demo_organization_id, 'email_sent',
     '{"subject": "Your roof estimate calculator", "delivered": false}', null,
     now() - interval '3 days' + interval '2 hours'),
    ('30000000-0000-0000-0000-000000000005', demo_organization_id, 'email_sent',
     '{"subject": "Getting your inquiries sorted before tax season", "delivered": false}', null,
     now() - interval '5 days' + interval '20 minutes'),
    ('30000000-0000-0000-0000-000000000006', demo_organization_id, 'email_sent',
     '{"subject": "Finding out why your payments fail", "delivered": false}', null,
     now() - interval '7 days' + interval '1 hour'),
    ('30000000-0000-0000-0000-000000000007', demo_organization_id, 'email_sent',
     '{"subject": "A client portal for Nair Interiors", "delivered": false}', null,
     now() - interval '9 days' + interval '45 minutes'),
    ('30000000-0000-0000-0000-000000000008', demo_organization_id, 'email_sent',
     '{"subject": "About your website", "delivered": false}', null,
     now() - interval '10 days' + interval '3 hours');
end;
$$;

-- Only the scheduler (running as postgres) may call it. Functions are executable by everyone by default.
revoke execute on function public.reset_demo_data() from public, anon, authenticated;

-- 16:00 UTC is midnight in Manila.
select cron.schedule('reset-demo-data', '0 16 * * *', 'select public.reset_demo_data()');
