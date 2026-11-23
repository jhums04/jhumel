import { render, screen } from '@testing-library/react'
import { describe, expect, it } from 'vitest'
import { createTestLead } from '../../test/createTestLead'
import { LeadCardDetails } from './LeadCardDetails'

const currentTime = new Date('2026-11-23T10:05:00Z')

describe('LeadCardDetails', () => {
  it('shows the name, company, score, estimate, and time since the lead arrived', () => {
    render(<LeadCardDetails lead={createTestLead()} currentTime={currentTime} />)

    expect(screen.getByText('Maria Santos')).toBeInTheDocument()
    expect(screen.getByText('Acme Dental')).toBeInTheDocument()
    expect(screen.getByText('High')).toBeInTheDocument()
    expect(screen.getByText('$3,000')).toBeInTheDocument()
    expect(screen.getByText('5 min ago')).toBeInTheDocument()
  })

  it.each([
    ['medium', 'Medium'],
    ['low', 'Low'],
  ] as const)('labels a %s score as %s', (score, label) => {
    render(<LeadCardDetails lead={createTestLead({ score })} currentTime={currentTime} />)

    expect(screen.getByText(label)).toBeInTheDocument()
  })

  it('marks a lead the AI has not scored yet', () => {
    render(<LeadCardDetails lead={createTestLead({ score: null })} currentTime={currentTime} />)

    expect(screen.getByText('Unscored')).toBeInTheDocument()
  })

  it('handles a lead without a company or an estimate', () => {
    render(
      <LeadCardDetails lead={createTestLead({ company: null, estimated_price: null })} currentTime={currentTime} />,
    )

    expect(screen.queryByText('Acme Dental')).not.toBeInTheDocument()
    expect(screen.getByText('No estimate')).toBeInTheDocument()
  })
})
