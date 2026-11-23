import { env } from '../../lib/env'
import { supabase } from '../../lib/supabase'

export type FollowUp = {
  leadId: string
  subject: string
  body: string
}

// Status 0 means the request never reached n8n (offline, or n8n is down).
export class SendFollowUpError extends Error {
  readonly status: number

  constructor(status: number) {
    super(`Sending the follow-up failed with status ${status}.`)
    this.status = status
    this.name = 'SendFollowUpError'
  }
}

function readDelivered(responseBody: unknown): boolean {
  if (typeof responseBody === 'object' && responseBody !== null && 'delivered' in responseBody) {
    return responseBody.delivered === true
  }
  throw new Error('The follow-up response did not say whether the reply was delivered.')
}

// n8n checks the access token with Supabase before it sends anything, so the request carries the current session.
// Resolves to false for demo leads, which are logged but never emailed.
export async function sendFollowUp({ leadId, subject, body }: FollowUp): Promise<boolean> {
  const { data } = await supabase.auth.getSession()
  if (data.session === null) {
    throw new SendFollowUpError(401)
  }

  let response: Response
  try {
    response = await fetch(env.followUpUrl, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${data.session.access_token}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ lead_id: leadId, subject, body }),
    })
  } catch {
    throw new SendFollowUpError(0)
  }

  if (!response.ok) {
    throw new SendFollowUpError(response.status)
  }
  return readDelivered(await response.json())
}

export function describeSendFollowUpError(error: Error): string {
  if (!(error instanceof SendFollowUpError)) {
    return 'Something went wrong while sending. Your draft is still here; try again.'
  }
  switch (error.status) {
    case 0:
      return 'Could not reach the server. Check your connection; your draft is still here.'
    case 401:
      return 'Your session expired. Sign in again, then send.'
    case 404:
      return 'This lead is no longer available.'
    case 422:
      return 'Check the subject and message: both are required, up to 150 and 5,000 characters.'
    default:
      return 'The email service did not respond. Your draft is still here; try again.'
  }
}
