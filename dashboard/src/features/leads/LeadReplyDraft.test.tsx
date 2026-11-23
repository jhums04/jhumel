import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { render, screen } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import { sendFollowUp, SendFollowUpError, type FollowUp } from '../followUps/sendFollowUp'
import { LeadReplyDraft } from './LeadReplyDraft'

vi.mock('../followUps/sendFollowUp', async (importOriginal) => ({
  ...(await importOriginal<typeof import('../followUps/sendFollowUp')>()),
  sendFollowUp: vi.fn<(followUp: FollowUp) => Promise<boolean>>(),
}))

const lead = {
  id: 'lead-1',
  email: 'maria@acme.dev',
  draft_subject: 'Your lead system quote',
  draft_body: 'Hi Maria,',
}

beforeEach(() => {
  vi.mocked(sendFollowUp).mockReset()
})

function renderDraft() {
  render(
    <QueryClientProvider client={new QueryClient()}>
      <LeadReplyDraft lead={lead} />
    </QueryClientProvider>,
  )
}

describe('LeadReplyDraft', () => {
  it('sends the edited reply and confirms who it went to', async () => {
    vi.mocked(sendFollowUp).mockResolvedValue(true)
    renderDraft()

    await userEvent.clear(screen.getByLabelText('Subject'))
    await userEvent.type(screen.getByLabelText('Subject'), 'Quick question')
    await userEvent.click(screen.getByRole('button', { name: 'Send reply' }))

    expect(await screen.findByRole('status')).toHaveTextContent('Reply sent to maria@acme.dev.')
    expect(vi.mocked(sendFollowUp).mock.calls[0]?.[0]).toEqual({
      leadId: 'lead-1',
      subject: 'Quick question',
      body: 'Hi Maria,',
    })
  })

  it('disables the button while sending', async () => {
    vi.mocked(sendFollowUp).mockReturnValue(new Promise(() => undefined))
    renderDraft()

    await userEvent.click(screen.getByRole('button', { name: 'Send reply' }))

    expect(screen.getByRole('button', { name: 'Sending...' })).toBeDisabled()
  })

  it('explains that demo replies are logged, not emailed', async () => {
    vi.mocked(sendFollowUp).mockResolvedValue(false)
    renderDraft()

    await userEvent.click(screen.getByRole('button', { name: 'Send reply' }))

    expect(await screen.findByRole('status')).toHaveTextContent('Demo account: the reply was logged but not emailed.')
  })

  it('shows an error and keeps the edited draft when sending fails', async () => {
    vi.mocked(sendFollowUp).mockRejectedValue(new SendFollowUpError(502))
    renderDraft()

    await userEvent.type(screen.getByLabelText('Message'), ' Thanks.')
    await userEvent.click(screen.getByRole('button', { name: 'Send reply' }))

    expect(await screen.findByRole('alert')).toHaveTextContent('The email service did not respond.')
    expect(screen.getByLabelText('Message')).toHaveValue('Hi Maria, Thanks.')
  })
})
