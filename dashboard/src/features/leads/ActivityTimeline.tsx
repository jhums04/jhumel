import { formatTimeSince } from '../../lib/format'
import { useCurrentTime } from '../../lib/useCurrentTime'
import { describeActivity } from './describeActivity'
import type { LeadActivity } from './leadsApi'

type ActivityTimelineProps = {
  activities: LeadActivity[]
  leadCreatedAt: string
}

type TimelineEntryProps = {
  text: string
  at: string
  currentTime: Date
  isLatest: boolean
}

// A dot per entry joined by a line to the next one; the latest dot is amber so the newest change stands out.
function TimelineEntry({ text, at, currentTime, isLatest }: TimelineEntryProps) {
  return (
    <li className="relative pb-5 pl-6 after:absolute after:top-4 after:bottom-0 after:left-[5px] after:w-0.5 after:bg-gray-200 last:pb-0 last:after:hidden">
      <span
        aria-hidden="true"
        className={`absolute top-1.5 left-0 z-10 size-3 rounded-full ring-4 ring-white ${isLatest ? 'bg-amber' : 'bg-gray-200'}`}
      />
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
    <ol>
      {activities.map((activity, index) => (
        <TimelineEntry
          key={activity.id}
          text={describeActivity(activity)}
          at={activity.created_at}
          currentTime={currentTime}
          isLatest={index === 0}
        />
      ))}
      <TimelineEntry
        text="Lead received from the website"
        at={leadCreatedAt}
        currentTime={currentTime}
        isLatest={activities.length === 0}
      />
    </ol>
  )
}
