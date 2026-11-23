import { formatTimeSince } from '../../lib/format'
import { useCurrentTime } from '../../lib/useCurrentTime'
import { describeActivity } from './describeActivity'
import type { LeadActivity } from './leadsApi'

type ActivityTimelineProps = {
  activities: LeadActivity[]
  leadCreatedAt: string
}

function TimelineEntry({ text, at, currentTime }: { text: string; at: string; currentTime: Date }) {
  return (
    <li className="border-l-2 border-gray-200 pl-4">
      <p>{text}</p>
      <p className="text-sm text-gray-600">
        <time dateTime={at} title={new Date(at).toLocaleString()}>
          {formatTimeSince(new Date(at), currentTime)}
        </time>
      </p>
    </li>
  )
}

// Newest first. The lead's arrival is not stored as an activity, so it is added as the oldest entry.
export function ActivityTimeline({ activities, leadCreatedAt }: ActivityTimelineProps) {
  const currentTime = useCurrentTime()

  return (
    <ol className="space-y-4">
      {activities.map((activity) => (
        <TimelineEntry
          key={activity.id}
          text={describeActivity(activity)}
          at={activity.created_at}
          currentTime={currentTime}
        />
      ))}
      <TimelineEntry text="Lead received from the website" at={leadCreatedAt} currentTime={currentTime} />
    </ol>
  )
}
