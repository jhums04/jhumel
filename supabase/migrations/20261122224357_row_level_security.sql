-- Security definer so policies can read organization_members without
-- recursing into that table's own RLS.
create function public.is_organization_member(target_organization_id uuid)
returns boolean
language sql
stable
security definer
set search_path = ''
as $$
  select exists (
    select 1 from public.organization_members
    where organization_id = target_organization_id
      and user_id = (select auth.uid())
  );
$$;

alter table public.organizations enable row level security;
alter table public.organization_members enable row level security;
alter table public.leads enable row level security;
alter table public.lead_activities enable row level security;

create policy "Members can view their organizations"
on public.organizations
for select
to authenticated
using (public.is_organization_member(id));

create policy "Users can view their own memberships"
on public.organization_members
for select
to authenticated
using (user_id = (select auth.uid()));

create policy "Members can view their organization's leads"
on public.leads
for select
to authenticated
using (public.is_organization_member(organization_id));

create policy "Members can update their organization's leads"
on public.leads
for update
to authenticated
using (public.is_organization_member(organization_id))
with check (public.is_organization_member(organization_id));

create policy "Members can view their organization's lead activities"
on public.lead_activities
for select
to authenticated
using (public.is_organization_member(organization_id));

create policy "Members can add notes to their organization's leads"
on public.lead_activities
for insert
to authenticated
with check (
  type = 'note'
  and created_by = (select auth.uid())
  and public.is_organization_member(organization_id)
  and exists (
    select 1 from public.leads
    where leads.id = lead_activities.lead_id
      and leads.organization_id = lead_activities.organization_id
  )
);

revoke update on public.leads from authenticated;
grant update (stage) on public.leads to authenticated;
