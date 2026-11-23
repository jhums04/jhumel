import type { ReactNode } from 'react'
import { formatCurrency } from '../../lib/format'
import { DrawerCard } from './DrawerCard'
import type { LeadDetails as LeadDetailsData } from './leadsApi'
import { LeadReplyDraft } from './LeadReplyDraft'
import { LeadStageSelect } from './LeadStageSelect'
import { getBudgetLabel, getServiceLabel, getTimelineLabel } from './serviceLabels'

const contactLinkClassName =
  'inline-flex min-h-11 max-w-full items-center rounded-full border border-gray-200 px-4 font-semibold text-navy break-all hover:border-navy hover:bg-amber-50'

function DetailRow({ label, children }: { label: string; children: ReactNode }) {
  return (
    <div className="grid grid-cols-[6.5rem_1fr] gap-3 py-1.5">
      <dt className="text-gray-600">{label}</dt>
      <dd className="min-w-0 break-words">{children}</dd>
    </div>
  )
}

export function LeadDetails({ lead }: { lead: LeadDetailsData }) {
  const scoreLabel = lead.score === null ? null : `${lead.score.charAt(0).toUpperCase()}${lead.score.slice(1)}`

  return (
    <>
      <DrawerCard title="Status">
        <LeadStageSelect lead={lead} />
        {lead.score_reason !== null && scoreLabel !== null && (
          <figure className="mt-5 rounded-r-xl border-l-4 border-amber bg-amber-50/60 px-4 py-3">
            <figcaption className="text-sm font-semibold text-amber-dark">Why the AI scored it {scoreLabel}</figcaption>
            <blockquote className="mt-1 font-serif text-lg leading-snug">{lead.score_reason}</blockquote>
          </figure>
        )}
      </DrawerCard>

      <DrawerCard title="Contact">
        <div className="flex flex-wrap gap-2">
          <a href={`mailto:${lead.email}`} className={contactLinkClassName}>
            {lead.email}
          </a>
          {lead.phone !== null && (
            <a href={`tel:${lead.phone}`} className={contactLinkClassName}>
              {lead.phone}
            </a>
          )}
        </div>
      </DrawerCard>

      <DrawerCard title="Request">
        {lead.services.length > 0 && (
          <ul aria-label="Services" className="mb-4 flex flex-wrap gap-2">
            {lead.services.map((service) => (
              <li key={service} className="rounded-full bg-gray-50 px-3 py-1 text-sm font-semibold">
                {getServiceLabel(service)}
              </li>
            ))}
          </ul>
        )}
        <dl>
          {lead.timeline !== null && <DetailRow label="Timeline">{getTimelineLabel(lead.timeline)}</DetailRow>}
          {lead.budget !== null && <DetailRow label="Budget">{getBudgetLabel(lead.budget)}</DetailRow>}
          {lead.estimated_price !== null && (
            <DetailRow label="Estimate">{formatCurrency(lead.estimated_price)}</DetailRow>
          )}
          <DetailRow label="Submitted">
            <time dateTime={lead.created_at}>{new Date(lead.created_at).toLocaleString()}</time>
          </DetailRow>
        </dl>
        {lead.message !== null && (
          <blockquote className="mt-4 rounded-xl bg-paper px-4 py-3 whitespace-pre-line">{lead.message}</blockquote>
        )}
      </DrawerCard>

      <DrawerCard
        title="Reply draft"
        aside={
          lead.draft_body !== null && (
            <span className="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-dark">Drafted by AI</span>
          )
        }
      >
        {/* Keyed on whether a draft exists, so the fields fill in when the score and draft arrive live. */}
        <LeadReplyDraft key={`${lead.id}-${lead.draft_body === null ? 'waiting' : 'ready'}`} lead={lead} />
      </DrawerCard>
    </>
  )
}
