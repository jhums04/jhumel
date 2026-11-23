import { useLocation, useNavigate, useParams } from 'react-router'
import { Button } from '../../components/Button'
import { Drawer } from '../../components/Drawer'
import { ActivityTimeline } from './ActivityTimeline'
import { DrawerCard } from './DrawerCard'
import { LeadDetails } from './LeadDetails'
import { LeadHeaderMeta } from './LeadHeaderMeta'
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
    <Drawer
      title={lead.data?.full_name ?? 'Lead'}
      subtitle={lead.data?.company}
      meta={lead.data ? <LeadHeaderMeta lead={lead.data} /> : undefined}
      onClose={close}
    >
      {lead.isPending && (
        <p role="status" className="text-gray-600">
          Loading lead...
        </p>
      )}
      {lead.isError && (
        <div role="alert" className="rounded-xl border border-error bg-error-background px-4 py-3 text-error">
          <p>Could not load this lead.</p>
          <Button variant="secondary" onClick={() => void lead.refetch()} className="mt-3">
            Try again
          </Button>
        </div>
      )}
      {lead.isSuccess && lead.data === null && (
        <p className="text-gray-600">This lead does not exist, or it belongs to an organization you are not in.</p>
      )}

      {lead.isSuccess && lead.data !== null && (
        <>
          <LeadDetails lead={lead.data} />
          <DrawerCard title="Activity">
            {activities.isPending && <p className="text-gray-600">Loading activity...</p>}
            {activities.isError && <p className="text-error">Could not load the activity history.</p>}
            {activities.isSuccess && (
              <ActivityTimeline activities={activities.data} leadCreatedAt={lead.data.created_at} />
            )}
          </DrawerCard>
        </>
      )}
    </Drawer>
  )
}
