import { useState, type FormEvent } from 'react'
import { Button } from '../../components/Button'
import { describeSendFollowUpError } from '../followUps/sendFollowUp'
import { useSendFollowUp } from '../followUps/useSendFollowUp'
import type { LeadDetails } from './leadsApi'

const fieldClassName = 'mt-2 block w-full rounded border border-gray-200 px-3 py-3 text-black focus:border-blue'

type LeadReplyDraftProps = {
  lead: Pick<LeadDetails, 'id' | 'email' | 'draft_subject' | 'draft_body'>
}

function SentMessage({ delivered, email }: { delivered: boolean; email: string }) {
  return (
    <p role="status" className="rounded border border-success bg-success-background px-4 py-3 text-success">
      {delivered
        ? `Reply sent to ${email}.`
        : "Demo account: the reply was logged but not emailed. In a real account it goes to the lead's inbox."}
    </p>
  )
}

// The AI's first reply, editable before sending. Edits live here until the drawer closes.
export function LeadReplyDraft({ lead }: LeadReplyDraftProps) {
  const [subject, setSubject] = useState(lead.draft_subject ?? '')
  const [body, setBody] = useState(lead.draft_body ?? '')
  const sendReply = useSendFollowUp()

  if (lead.draft_subject === null || lead.draft_body === null) {
    return <p className="text-gray-600">The AI draft appears here once the lead is scored.</p>
  }

  function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault()
    sendReply.mutate({ leadId: lead.id, subject, body })
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-5">
      <div>
        <label htmlFor={`reply-subject-${lead.id}`} className="block text-sm font-semibold">
          Subject
        </label>
        <input
          id={`reply-subject-${lead.id}`}
          type="text"
          required
          maxLength={150}
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
          required
          maxLength={5000}
          value={body}
          onChange={(event) => setBody(event.target.value)}
          className={fieldClassName}
        />
      </div>

      {sendReply.isError && (
        <p role="alert" className="rounded border border-error bg-error-background px-4 py-3 text-error">
          {describeSendFollowUpError(sendReply.error)}
        </p>
      )}
      {sendReply.isSuccess && <SentMessage delivered={sendReply.data} email={lead.email} />}

      <Button type="submit" disabled={sendReply.isPending}>
        {sendReply.isPending ? 'Sending...' : 'Send reply'}
      </Button>
    </form>
  )
}
