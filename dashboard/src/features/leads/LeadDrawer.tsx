import { useLocation, useNavigate, useParams } from 'react-router'
import { Button } from '../../components/Button'
import { Drawer } from '../../components/Drawer'
import { ActivityTimeline } from './ActivityTimeline'
import { LeadDetails } from './LeadDetails'
import { useLead, useLeadActivities } from './useLead'

export function LeadDrawer() {
  const { leadId = '' } = useParams()
  const navigate = useNavigate()
  const location = useLocation()
  const lead = useLead(leadId)
  const activities = useLeadActivities(leadId)

  // Back to the board, keeping ?organization= so the same board stays open.
  function close() {
    void navigate({ pathname: '/', search: location.search })
  }

  return (
    <Drawer title={lead.data?.full_name ?? 'Lead'} subtitle={lead.data?.company} onClose={close}>
      {lead.isPending && (
        <p role="status" className="px-6 text-gray-600">
          Loading lead...
        </p>
      )}
      {lead.isError && (
        <div role="alert" className="mx-6 rounded border border-error bg-error-background px-4 py-3 text-error">
          <p>Could not load this lead.</p>
          <Button variant="secondary" onClick={() => void lead.refetch()} className="mt-3">
            Try again
          </Button>
        </div>
      )}
      {lead.isSuccess && lead.data === null && (
        <p className="px-6 text-gray-600">This lead does not exist, or it belongs to an organization you are not in.</p>
      )}

      {lead.isSuccess && lead.data !== null && (
        <>
          <LeadDetails lead={lead.data} />
          <section className="border-t border-gray-200 px-6 py-6">
            <h3 className="mb-4 text-sm font-semibold text-gray-600">Activity</h3>
            {activities.isPending && <p className="text-gray-600">Loading activity...</p>}
            {activities.isError && <p className="text-error">Could not load the activity history.</p>}
            {activities.isSuccess && (
              <ActivityTimeline activities={activities.data} leadCreatedAt={lead.data.created_at} />
            )}
          </section>
        </>
      )}
    </Drawer>
  )
}
