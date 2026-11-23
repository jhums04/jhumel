import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { render, screen } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { describe, expect, it, vi } from 'vitest'
import { createTestLeadDetails } from '../../test/createTestLeadDetails'
import { updateLeadStage } from './leadsApi'
import { LeadStageSelect } from './LeadStageSelect'

vi.mock('./leadsApi', () => ({
  updateLeadStage: vi.fn<(leadId: string, stage: string) => Promise<void>>(),
}))

function renderStageSelect() {
  render(
    <QueryClientProvider client={new QueryClient()}>
      <LeadStageSelect lead={createTestLeadDetails({ id: 'lead-1', stage: 'new' })} />
    </QueryClientProvider>,
  )
}

describe('LeadStageSelect', () => {
  it('moves the lead to the chosen stage', async () => {
    vi.mocked(updateLeadStage).mockResolvedValue(undefined)
    renderStageSelect()

    await userEvent.selectOptions(screen.getByLabelText('Stage'), 'Proposal sent')

    expect(updateLeadStage).toHaveBeenCalledWith('lead-1', 'proposal_sent')
  })

  it('explains when the move fails', async () => {
    vi.mocked(updateLeadStage).mockRejectedValue(new Error('permission denied'))
    renderStageSelect()

    await userEvent.selectOptions(screen.getByLabelText('Stage'), 'Won')

    expect(await screen.findByRole('alert')).toHaveTextContent('Could not move this lead to Won. Try again.')
  })
})
