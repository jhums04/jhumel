begin;
select plan(4);

insert into auth.users (id, email) values
  ('10000000-0000-0000-0000-000000000001', 'member@example.com');

insert into public.organizations (id, name, slug) values
  ('a0000000-0000-0000-0000-000000000001', 'Own Organization', 'test-own');

insert into public.organization_members (organization_id, user_id) values
  ('a0000000-0000-0000-0000-000000000001', '10000000-0000-0000-0000-000000000001');

-- now() is fixed for the whole transaction, so backdate updated_at to see it move.
insert into public.leads (id, organization_id, external_id, full_name, email, updated_at) values
  ('b0000000-0000-0000-0000-000000000001', 'a0000000-0000-0000-0000-000000000001', 'test-own-1', 'Own Lead', 'own@example.com', now() - interval '1 day');

set local role authenticated;
set local request.jwt.claims to '{"sub": "10000000-0000-0000-0000-000000000001", "role": "authenticated"}';

update public.leads set stage = 'contacted' where id = 'b0000000-0000-0000-0000-000000000001';

select results_eq(
  $$ select type, details, created_by from public.lead_activities
     where lead_id = 'b0000000-0000-0000-0000-000000000001' $$,
  $$ values ('stage_changed', '{"from": "new", "to": "contacted"}'::jsonb, '10000000-0000-0000-0000-000000000001'::uuid) $$,
  'Changing the stage logs a stage_changed activity with from, to, and the user'
);

update public.leads set stage = 'contacted' where id = 'b0000000-0000-0000-0000-000000000001';

select is(
  (select count(*) from public.lead_activities where lead_id = 'b0000000-0000-0000-0000-000000000001'),
  1::bigint,
  'Saving the same stage again logs nothing'
);

reset role;

update public.leads set company = 'Acme' where id = 'b0000000-0000-0000-0000-000000000001';

select is(
  (select count(*) from public.lead_activities where lead_id = 'b0000000-0000-0000-0000-000000000001'),
  1::bigint,
  'Changing another column logs nothing'
);

select is(
  (select updated_at from public.leads where id = 'b0000000-0000-0000-0000-000000000001'),
  now(),
  'Updating a lead sets updated_at'
);

select * from finish();
rollback;
