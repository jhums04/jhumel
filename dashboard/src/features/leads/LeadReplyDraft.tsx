import { useState } from 'react'
import type { LeadDetails } from './leadsApi'

const fieldClassName = 'mt-2 block w-full rounded border border-gray-200 px-3 py-3 text-black focus:border-blue'

type LeadReplyDraftProps = {
  lead: Pick<LeadDetails, 'id' | 'draft_subject' | 'draft_body'>
}

// The AI's first reply, editable before sending. Edits live here until the drawer closes.
export function LeadReplyDraft({ lead }: LeadReplyDraftProps) {
  const [subject, setSubject] = useState(lead.draft_subject ?? '')
  const [body, setBody] = useState(lead.draft_body ?? '')

  if (lead.draft_subject === null || lead.draft_body === null) {
    return <p className="text-gray-600">The AI draft appears here once the lead is scored.</p>
  }

  return (
    <div className="space-y-5">
      <div>
        <label htmlFor={`reply-subject-${lead.id}`} className="block text-sm font-semibold">
          Subject
        </label>
        <input
          id={`reply-subject-${lead.id}`}
          type="text"
          value={subject}
          onChange={(event) => setSubject(event.target.value)}
          className={fieldClassName}
        />
      </div>
      <div>
        <label htmlFor={`reply-body-${lead.id}`} className="block text-sm font-semibold">
          Message
        </label>
        <textarea
          id={`reply-body-${lead.id}`}
          rows={10}
          value={body}
          onChange={(event) => setBody(event.target.value)}
          className={fieldClassName}
        />
      </div>
    </div>
  )
}
