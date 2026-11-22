begin;
select plan(5);

insert into auth.users (id, email) values
  ('10000000-0000-0000-0000-000000000001', 'member@example.com'),
  ('10000000-0000-0000-0000-000000000002', 'outsider@example.com');

insert into public.organizations (id, name, slug) values
  ('a0000000-0000-0000-0000-000000000001', 'Own Organization', 'test-own'),
  ('a0000000-0000-0000-0000-000000000002', 'Other Organization', 'test-other');

insert into public.organization_members (organization_id, user_id) values
  ('a0000000-0000-0000-0000-000000000001', '10000000-0000-0000-0000-000000000001'),
  ('a0000000-0000-0000-0000-000000000002', '10000000-0000-0000-0000-000000000002');

set local role authenticated;
set local request.jwt.claims to '{"sub": "10000000-0000-0000-0000-000000000001", "role": "authenticated"}';

select results_eq(
  'select slug from public.organizations',
  array['test-own'],
  'A member sees only their own organization'
);

select results_eq(
  'select user_id from public.organization_members',
  array['10000000-0000-0000-0000-000000000001'::uuid],
  'A user sees only their own memberships'
);

select throws_ok(
  $$ insert into public.organizations (name, slug) values ('New', 'test-new') $$,
  '42501',
  null,
  'A member cannot create organizations'
);

reset role;
set local role anon;
set local request.jwt.claims to '{"role": "anon"}';

select is_empty(
  'select id from public.organizations',
  'Anonymous users see no organizations'
);

select is_empty(
  'select user_id from public.organization_members',
  'Anonymous users see no memberships'
);

select * from finish();
rollback;
