import { useQuery } from '@tanstack/react-query'
import { supabase } from '../../lib/supabase'

async function countVisibleLeads(): Promise<number> {
  const { count, error } = await supabase.from('leads').select('*', { count: 'exact', head: true })
  if (error) {
    throw error
  }
  return count ?? 0
}

// Temporary home page until the pipeline board (step 4.3). Row-level security limits the count to the user's organization.
export function ConnectionCheckPage() {
  const visibleLeads = useQuery({ queryKey: ['visible-leads-count'], queryFn: countVisibleLeads })

  return (
    <>
      <h1 className="text-2xl font-bold">Pipeline</h1>
      {visibleLeads.isPending && <p className="mt-4 text-gray-600">Loading leads...</p>}
      {visibleLeads.isError && (
        <p role="alert" className="mt-4 text-error">
          Could not load leads: {visibleLeads.error.message}
        </p>
      )}
      {visibleLeads.isSuccess && (
        <p className="mt-4 text-gray-600">Leads in your organization: {visibleLeads.data}.</p>
      )}
    </>
  )
}
