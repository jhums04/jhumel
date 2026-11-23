-- The dashboard subscribes to lead changes so new leads and score updates appear without a refresh.
-- Realtime checks the "Members can view their organization's leads" policy before sending each change,
-- so subscribers only receive leads from their own organizations.
alter publication supabase_realtime add table public.leads;
