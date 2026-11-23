begin;
select plan(10);

-- Runs the same statements the n8n workflows run, as the n8n_workflows role. pgTAP itself runs as postgres:
-- each check switches role only inside its own block, and a failed check rolls back its role switch too.

select lives_ok(
  $test$do $role$ begin set role n8n_workflows; insert into public.leads (organization_id, external_id, full_name, email, services, source) select id, 'role-test-1', 'Role Test', 'role@example.com', '{integrations}', 'website' from public.organizations where slug = 'jhumel' on conflict (organization_id, external_id) do nothing; reset role; end $role$$test$,
  'lead intake can save a new lead'
);

select lives_ok(
  $test$do $role$ begin set role n8n_workflows; perform 1 from public.leads where external_id = 'role-test-1' having count(*) = 1; if not found then raise exception 'lead not readable'; end if; reset role; end $role$$test$,
  'lead intake can read the saved lead back'
);

select lives_ok(
  $test$do $role$ begin set role n8n_workflows; update public.leads set score = 'high', score_reason = 'Clear need.', draft_subject = 'Hi', draft_body = 'Hello' where external_id = 'role-test-1'; reset role; end $role$$test$,
  'lead intake can store the AI score and draft'
);

select lives_ok(
  $test$do $role$ begin set role n8n_workflows; perform leads.id from public.leads join public.organization_members on organization_members.organization_id = leads.organization_id where leads.external_id = 'role-test-1'; reset role; end $role$$test$,
  'send follow-up can check membership'
);

select lives_ok(
  $test$do $role$ begin set role n8n_workflows; insert into public.lead_activities (lead_id, organization_id, type, details) select id, organization_id, 'email_sent', '{"subject": "Hi", "delivered": true}' from public.leads where external_id = 'role-test-1'; reset role; end $role$$test$,
  'send follow-up can log the reply'
);

select lives_ok(
  $test$do $role$ begin set role n8n_workflows; update public.leads set stage = 'contacted' where external_id = 'role-test-1' and stage = 'new'; reset role; end $role$$test$,
  'send follow-up can move the lead to contacted'
);

select throws_ok(
  $test$do $role$ begin set role n8n_workflows; update public.leads set email = 'changed@example.com' where external_id = 'role-test-1'; reset role; end $role$$test$,
  '42501',
  null,
  'it cannot change contact details'
);

select throws_ok(
  $test$do $role$ begin set role n8n_workflows; delete from public.leads where external_id = 'role-test-1'; reset role; end $role$$test$,
  '42501',
  null,
  'it cannot delete leads'
);

select throws_ok(
  $test$do $role$ begin set role n8n_workflows; perform count(*) from auth.users; reset role; end $role$$test$,
  '42501',
  null,
  'it cannot read sign-in accounts'
);

select throws_ok(
  $test$do $role$ begin set role n8n_workflows; insert into public.organizations (name, slug) values ('Sneaky', 'sneaky'); reset role; end $role$$test$,
  '42501',
  null,
  'it cannot create organizations'
);

select * from finish();
rollback;
