-- The "Start a project" brief asks for a budget range instead of showing a price estimate.
-- Quote-form leads leave it empty. The values match the plugin's ProjectBrief::BUDGETS keys.
alter table public.leads
  add column budget text
  check (budget in ('under_1k', '1k_to_3k', '3k_to_10k', 'over_10k', 'not_sure'));
