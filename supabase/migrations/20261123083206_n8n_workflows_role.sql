-- A least-privilege role for n8n. In production n8n connects as this role instead of postgres, so a leaked
-- n8n credential can only do what the workflows do: save leads, store scores, log follow-ups, read memberships.
-- It has no login here; set its password once per environment (see infra/SERVER_SETUP.md):
--   alter role n8n_workflows with login password '...';

create role n8n_workflows nologin noinherit;

-- postgres is not a superuser on Supabase, so it needs membership to manage this role and to test as it.
grant n8n_workflows to postgres with set true;

grant usage on schema public to n8n_workflows;

-- Lead intake: find the organization by slug, insert the lead, store the AI score and draft.
-- Send follow-up: find the lead for a member, log the reply, move the lead to contacted.
grant select on public.organizations, public.organization_members to n8n_workflows;
grant select, insert on public.leads to n8n_workflows;
grant update (score, score_reason, draft_subject, draft_body, stage) on public.leads to n8n_workflows;
grant insert on public.lead_activities to n8n_workflows;
grant usage, select on all sequences in schema public to n8n_workflows;

-- Row-level security applies to this role like any other, so it gets explicit policies. Workflows act on any
-- organization (they check membership themselves where it matters), hence "true".
create policy "n8n reads organizations" on public.organizations for select to n8n_workflows using (true);
create policy "n8n reads memberships" on public.organization_members for select to n8n_workflows using (true);
create policy "n8n reads leads" on public.leads for select to n8n_workflows using (true);
create policy "n8n adds leads" on public.leads for insert to n8n_workflows with check (true);
create policy "n8n updates leads" on public.leads for update to n8n_workflows using (true) with check (true);
create policy "n8n logs activities" on public.lead_activities for insert to n8n_workflows with check (true);
