begin;
select plan(7);

insert into auth.users (id, email) values
  ('10000000-0000-0000-0000-000000000001', 'member@example.com'),
  ('10000000-0000-0000-0000-000000000002', 'outsider@example.com');

insert into public.organizations (id, name, slug) values
  ('a0000000-0000-0000-0000-000000000001', 'Own Organization', 'test-own'),
  ('a0000000-0000-0000-0000-000000000002', 'Other Organization', 'test-other');

insert into public.organization_members (organization_id, user_id) values
  ('a0000000-0000-0000-0000-000000000001', '10000000-0000-0000-0000-000000000001'),
  ('a0000000-0000-0000-0000-000000000002', '10000000-0000-0000-0000-000000000002');

insert into public.leads (id, organization_id, external_id, full_name, email) values
  ('b0000000-0000-0000-0000-000000000001', 'a0000000-0000-0000-0000-000000000001', 'test-own-1', 'Own Lead', 'own@example.com'),
  ('b0000000-0000-0000-0000-000000000002', 'a0000000-0000-0000-0000-000000000002', 'test-other-1', 'Other Lead', 'other@example.com');

insert into public.lead_activities (lead_id, organization_id, type, details) values
  ('b0000000-0000-0000-0000-000000000001', 'a0000000-0000-0000-0000-000000000001', 'note', '{"text": "Own note"}'),
  ('b0000000-0000-0000-0000-000000000002', 'a0000000-0000-0000-0000-000000000002', 'note', '{"text": "Other note"}');

set local role authenticated;
set local request.jwt.claims to '{"sub": "10000000-0000-0000-0000-000000000001", "role": "authenticated"}';

select results_eq(
  $$ select details ->> 'text' from public.lead_activities $$,
  array['Own note'],
  'A member sees their organization''s activities and not another organization''s'
);

select lives_ok(
  $$ insert into public.lead_activities (lead_id, organization_id, type, created_by)
     values ('b0000000-0000-0000-0000-000000000001', 'a0000000-0000-0000-0000-000000000001', 'note', '10000000-0000-0000-0000-000000000001') $$,
  'A member can insert a note'
);

select throws_ok(
  $$ insert into public.lead_activities (lead_id, organization_id, type, created_by)
     values ('b0000000-0000-0000-0000-000000000001', 'a0000000-0000-0000-0000-000000000001', 'email_sent', '10000000-0000-0000-0000-000000000001') $$,
  '42501',
  null,
  'A member cannot insert an email_sent activity'
);

select throws_ok(
  $$ insert into public.lead_activities (lead_id, organization_id, type, created_by)
     values ('b0000000-0000-0000-0000-000000000001', 'a0000000-0000-0000-0000-000000000001', 'note', '10000000-0000-0000-0000-000000000002') $$,
  '42501',
  null,
  'A member cannot insert a note signed as another user'
);

select throws_ok(
  $$ insert into public.lead_activities (lead_id, organization_id, type, created_by)
     values ('b0000000-0000-0000-0000-000000000002', 'a0000000-0000-0000-0000-000000000001', 'note', '10000000-0000-0000-0000-000000000001') $$,
  '42501',
  null,
  'A member cannot attach a note to another organization''s lead'
);

select throws_ok(
  $$ insert into public.lead_activities (lead_id, organization_id, type, created_by)
     values ('b0000000-0000-0000-0000-000000000002', 'a0000000-0000-0000-0000-000000000002', 'note', '10000000-0000-0000-0000-000000000001') $$,
  '42501',
  null,
  'A member cannot insert a note into another organization'
);

reset role;
set local role anon;
set local request.jwt.claims to '{"role": "anon"}';

select is_empty(
  'select id from public.lead_activities',
  'Anonymous users see no activities'
);

select * from finish();
rollback;
