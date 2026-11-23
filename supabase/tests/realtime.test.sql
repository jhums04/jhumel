begin;
select plan(1);

select ok(
  exists (
    select 1
    from pg_publication_tables
    where pubname = 'supabase_realtime' and schemaname = 'public' and tablename = 'leads'
  ),
  'leads changes are published to Supabase Realtime'
);

select * from finish();
rollback;
