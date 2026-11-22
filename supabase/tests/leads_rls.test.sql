begin;
select plan(9);

insert into auth.users (id, email) values
  ('10000000-0000-0000-0000-000000000001', 'member@example.com');

insert into public.organizations (id, name, slug) values
  ('a0000000-0000-0000-0000-000000000001', 'Own Organization', 'test-own'),
  ('a0000000-0000-0000-0000-000000000002', 'Other Organization', 'test-other');

insert into public.organization_members (organization_id, user_id) values
  ('a0000000-0000-0000-0000-000000000001', '10000000-0000-0000-0000-000000000001');

insert into public.leads (id, organization_id, external_id, full_name, email) values
  ('b0000000-0000-0000-0000-000000000001', 'a0000000-0000-0000-0000-000000000001', 'test-own-1', 'Own Lead', 'own@example.com'),
  ('b0000000-0000-0000-0000-000000000002', 'a0000000-0000-0000-0000-000000000002', 'test-other-1', 'Other Lead', 'other@example.com');

set local role authenticated;
set local request.jwt.claims to '{"sub": "10000000-0000-0000-0000-000000000001", "role": "authenticated"}';

select results_eq(
  'select full_name from public.leads',
  array['Own Lead'],
  'A member sees their organization''s leads and not another organization''s'
);

select lives_ok(
  $$ update public.leads set stage = 'contacted' where id = 'b0000000-0000-0000-0000-000000000001' $$,
  'A member can update stage on their organization''s lead'
);

update public.leads set stage = 'won' where id = 'b0000000-0000-0000-0000-000000000002';

select throws_ok(
  $$ update public.leads set email = 'changed@example.com' where id = 'b0000000-0000-0000-0000-000000000001' $$,
  '42501',
  null,
  'A member cannot update email'
);

select throws_ok(
  $$ update public.leads set score = 'high' where id = 'b0000000-0000-0000-0000-000000000001' $$,
  '42501',
  null,
  'A member cannot update score'
);

select throws_ok(
  $$ insert into public.leads (organization_id, external_id, full_name, email)
     values ('a0000000-0000-0000-0000-000000000001', 'test-own-2', 'New Lead', 'new@example.com') $$,
  '42501',
  null,
  'A member cannot insert leads'
);

delete from public.leads where id = 'b0000000-0000-0000-0000-000000000001';

reset role;
set local role anon;
set local request.jwt.claims to '{"role": "anon"}';

select is_empty(
  'select id from public.leads',
  'Anonymous users see no leads'
);

reset role;

select is(
  (select stage from public.leads where id = 'b0000000-0000-0000-0000-000000000001'),
  'contacted'::public.lead_stage,
  'The stage update on the member''s own lead was saved'
);

select is(
  (select stage from public.leads where id = 'b0000000-0000-0000-0000-000000000002'),
  'new'::public.lead_stage,
  'A member cannot update another organization''s lead'
);

select isnt_empty(
  $$ select id from public.leads where id = 'b0000000-0000-0000-0000-000000000001' $$,
  'A member cannot delete leads'
);

select * from finish();
rollback;
