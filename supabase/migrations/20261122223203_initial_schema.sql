create type public.lead_stage as enum ('new', 'contacted', 'proposal_sent', 'won', 'lost');
create type public.lead_score as enum ('high', 'medium', 'low');

create table public.organizations (
  id uuid primary key default gen_random_uuid(),
  name text not null,
  slug text not null unique,
  is_demo boolean not null default false,
  created_at timestamptz not null default now()
);

create table public.organization_members (
  organization_id uuid not null references public.organizations on delete cascade,
  user_id uuid not null references auth.users on delete cascade,
  role text not null default 'member' check (role in ('owner', 'member')),
  primary key (organization_id, user_id)
);

create table public.leads (
  id uuid primary key default gen_random_uuid(),
  organization_id uuid not null references public.organizations on delete cascade,
  external_id text not null,
  full_name text not null,
  email text not null,
  phone text,
  company text,
  services text[] not null default '{}',
  timeline text,
  message text,
  estimated_price numeric(10, 2),
  stage public.lead_stage not null default 'new',
  score public.lead_score,
  score_reason text,
  draft_subject text,
  draft_body text,
  source text not null default 'website',
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now(),
  unique (organization_id, external_id)
);

create index leads_organization_stage_idx on public.leads (organization_id, stage);
create index leads_organization_created_idx on public.leads (organization_id, created_at desc);

create table public.lead_activities (
  id bigint generated always as identity primary key,
  lead_id uuid not null references public.leads on delete cascade,
  organization_id uuid not null references public.organizations on delete cascade,
  type text not null check (type in ('stage_changed', 'email_sent', 'note')),
  details jsonb not null default '{}',
  created_by uuid references auth.users,
  created_at timestamptz not null default now()
);

create index lead_activities_lead_idx on public.lead_activities (lead_id, created_at desc);

create function public.set_updated_at()
returns trigger
language plpgsql
set search_path = ''
as $$
begin
  new.updated_at = now();
  return new;
end;
$$;

create trigger set_updated_at
before update on public.leads
for each row
execute function public.set_updated_at();

-- Security definer because RLS lets members insert only 'note' activities,
-- yet their stage changes must still be logged.
create function public.log_stage_change()
returns trigger
language plpgsql
security definer
set search_path = ''
as $$
begin
  insert into public.lead_activities (lead_id, organization_id, type, details, created_by)
  values (
    new.id,
    new.organization_id,
    'stage_changed',
    jsonb_build_object('from', old.stage, 'to', new.stage),
    (select auth.uid())
  );
  return new;
end;
$$;

create trigger log_stage_change
after update of stage on public.leads
for each row
when (old.stage is distinct from new.stage)
execute function public.log_stage_change();
