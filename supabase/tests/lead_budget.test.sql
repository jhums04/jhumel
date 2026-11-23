begin;
select plan(3);

select has_column('public', 'leads', 'budget', 'leads have a budget column for project briefs');

insert into public.leads (organization_id, external_id, full_name, email, budget)
values ((select id from public.organizations where slug = 'jhumel'), 'budget-test-1', 'Budget Test', 'budget@example.com', '3k_to_10k');

select is(
  (select budget from public.leads where external_id = 'budget-test-1'),
  '3k_to_10k',
  'a known budget range is stored'
);

select throws_ok(
  $$insert into public.leads (organization_id, external_id, full_name, email, budget)
    values ((select id from public.organizations where slug = 'jhumel'), 'budget-test-2', 'Budget Test', 'budget@example.com', 'a million')$$,
  '23514',
  null,
  'an unknown budget value is rejected'
);

select * from finish();
rollback;
