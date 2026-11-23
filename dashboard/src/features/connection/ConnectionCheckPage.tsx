import { useQuery } from '@tanstack/react-query'
import { supabase } from '../../lib/supabase'

async function countVisibleLeads(): Promise<number> {
  const { count, error } = await supabase.from('leads').select('*', { count: 'exact', head: true })
  if (error) {
    throw error
  }
  return count ?? 0
}

// Temporary home page until the pipeline board (step 4.3). Signed out, row-level security hides every lead.
export function ConnectionCheckPage() {
  const visibleLeads = useQuery({ queryKey: ['visible-leads-count'], queryFn: countVisibleLeads })

  return (
    <main className="mx-auto max-w-2xl px-4 py-16">
      <h1 className="text-3xl font-bold">Lead Engine</h1>
      {visibleLeads.isPending && <p className="mt-4 text-gray-600">Connecting to Supabase...</p>}
      {visibleLeads.isError && (
        <p role="alert" className="mt-4 text-error">
          Could not reach Supabase: {visibleLeads.error.message}
        </p>
      )}
      {visibleLeads.isSuccess && (
        <p className="mt-4 text-gray-600">
          Connected to Supabase. Leads visible while signed out: {visibleLeads.data}.
        </p>
      )}
    </main>
  )
}
