begin;
select plan(9);

-- Uses the demo organization from seed.sql; reset_demo_data() expects exactly one organization with is_demo.
insert into public.organizations (id, name, slug) values
  ('a0000000-0000-0000-0000-000000000009', 'Real Client Organization', 'test-real');

insert into public.leads (organization_id, external_id, full_name, email) values
  ((select id from public.organizations where is_demo), 'visitor-test-1', 'Visitor Test Lead', 'visitor@example.com'),
  ('a0000000-0000-0000-0000-000000000009', 'real-test-1', 'Real Lead', 'real@example.com');

select public.reset_demo_data();

select is(
  (select count(*)::int from public.leads where organization_id = (select id from public.organizations where is_demo)),
  8,
  'the demo organization has the eight sample leads again'
);

select is(
  (select count(*)::int from public.leads where external_id = 'visitor-test-1'),
  0,
  'leads that visitors added to the demo are removed'
);

select is(
  (select count(*)::int from public.leads where external_id = 'real-test-1'),
  1,
  'leads of other organizations are untouched'
);

select results_eq(
  $$select stage::text as stage_name, count(*)::int from public.leads
    where organization_id = (select id from public.organizations where is_demo)
    group by stage order by stage$$,
  $$values ('new', 2), ('contacted', 2), ('proposal_sent', 1), ('won', 2), ('lost', 1)$$,
  'the sample leads are spread across every stage'
);

select is(
  (select count(*)::int from public.lead_activities
   where organization_id = (select id from public.organizations where is_demo) and type = 'email_sent'),
  6,
  'the sample follow-ups are back, so response time has data'
);

select ok(
  not exists (
    select 1 from public.lead_activities
    where organization_id = (select id from public.organizations where is_demo)
      and type = 'email_sent' and (details ->> 'delivered')::boolean
  ),
  'no sample follow-up claims to have been delivered'
);

select is(
  (select prosecdef from pg_proc where proname = 'reset_demo_data'),
  true,
  'reset_demo_data runs as its owner, so row-level security does not block it'
);

set local role authenticated;
select throws_ok(
  'select public.reset_demo_data()',
  '42501',
  null,
  'signed-in users cannot reset the demo'
);
reset role;

select is(
  (select schedule from cron.job where jobname = 'reset-demo-data'),
  '0 16 * * *',
  'the reset is scheduled every night at midnight Manila time (16:00 UTC)'
);

select * from finish();
rollback;
