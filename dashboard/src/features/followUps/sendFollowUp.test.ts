import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest'
import { createTestSession } from '../../test/createTestSession'
import { describeSendFollowUpError, sendFollowUp, SendFollowUpError } from './sendFollowUp'

const authMocks = vi.hoisted(() => ({
  getSession: vi.fn<() => Promise<{ data: { session: ReturnType<typeof createTestSession> | null } }>>(),
}))

vi.mock('../../lib/supabase', () => ({ supabase: { auth: authMocks } }))
vi.mock('../../lib/env', () => ({ env: { followUpUrl: 'http://n8n.test/webhook/send-follow-up' } }))

const followUp = { leadId: 'lead-1', subject: 'Your quote', body: 'Hi Maria,' }
const fetchMock = vi.fn<typeof fetch>()

beforeEach(() => {
  authMocks.getSession.mockResolvedValue({ data: { session: createTestSession('jhumel@jhumel.dev') } })
  vi.stubGlobal('fetch', fetchMock)
})

afterEach(() => {
  vi.unstubAllGlobals()
})

function respondWith(status: number, body: unknown) {
  fetchMock.mockResolvedValue(new Response(JSON.stringify(body), { status }))
}

async function sendAndCatch(): Promise<SendFollowUpError> {
  try {
    await sendFollowUp(followUp)
  } catch (error) {
    if (error instanceof SendFollowUpError) {
      return error
    }
  }
  throw new Error('Expected a SendFollowUpError.')
}

describe('sendFollowUp', () => {
  it("posts the reply with the user's access token", async () => {
    respondWith(200, { delivered: true })

    await sendFollowUp(followUp)

    expect(fetchMock).toHaveBeenCalledWith('http://n8n.test/webhook/send-follow-up', {
      method: 'POST',
      headers: { Authorization: 'Bearer test-access-token', 'Content-Type': 'application/json' },
      body: JSON.stringify({ lead_id: 'lead-1', subject: 'Your quote', body: 'Hi Maria,' }),
    })
  })

  it('reports whether the reply was emailed or only logged for the demo', async () => {
    respondWith(200, { delivered: true })
    await expect(sendFollowUp(followUp)).resolves.toBe(true)

    respondWith(200, { delivered: false })
    await expect(sendFollowUp(followUp)).resolves.toBe(false)
  })

  it('fails with the response status so the drawer can explain it', async () => {
    respondWith(404, { message: 'Lead not found.' })

    expect((await sendAndCatch()).status).toBe(404)
  })

  it('fails with status 0 when n8n cannot be reached', async () => {
    fetchMock.mockRejectedValue(new TypeError('Failed to fetch'))

    expect((await sendAndCatch()).status).toBe(0)
  })

  it('does not call n8n without a session', async () => {
    authMocks.getSession.mockResolvedValue({ data: { session: null } })

    expect((await sendAndCatch()).status).toBe(401)
    expect(fetchMock).not.toHaveBeenCalled()
  })
})

describe('describeSendFollowUpError', () => {
  it.each([
    [0, 'Could not reach the server'],
    [401, 'Your session expired'],
    [404, 'no longer available'],
    [422, 'Check the subject and message'],
    [502, 'The email service did not respond'],
  ])('explains status %s', (status, expected) => {
    expect(describeSendFollowUpError(new SendFollowUpError(status))).toContain(expected)
  })
})
