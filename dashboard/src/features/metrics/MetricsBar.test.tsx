import { render, screen } from '@testing-library/react'
import { describe, expect, it } from 'vitest'
import type { PipelineMetrics } from './calculatePipelineMetrics'
import { MetricsBar } from './MetricsBar'

const metrics: PipelineMetrics = {
  leadsToday: 3,
  totalLeads: 9,
  openPipelineValue: 12_600,
  openLeads: 5,
  wonLeads: 2,
  lostLeads: 1,
  conversionRate: 2 / 3,
  averageResponseMilliseconds: 200 * 60_000,
  repliedLeads: 1,
}

describe('MetricsBar', () => {
  it('shows each figure with its context', () => {
    render(<MetricsBar metrics={metrics} />)

    expect(screen.getByText('3')).toBeInTheDocument()
    expect(screen.getByText('9 in total')).toBeInTheDocument()
    expect(screen.getByText('$12.6K')).toBeInTheDocument()
    expect(screen.getByText('across 5 open leads')).toBeInTheDocument()
    expect(screen.getByText('67%')).toBeInTheDocument()
    expect(screen.getByText('2 won · 1 lost')).toBeInTheDocument()
    expect(screen.getByText('3 h 20 min')).toBeInTheDocument()
    expect(screen.getByText('across 1 replied lead')).toBeInTheDocument()
  })

  it('says so instead of showing 0% or a blank time', () => {
    render(<MetricsBar metrics={{ ...metrics, conversionRate: null, averageResponseMilliseconds: null, repliedLeads: 0 }} />)

    expect(screen.getByText('No closed leads yet')).toBeInTheDocument()
    expect(screen.getByText('No replies yet')).toBeInTheDocument()
  })
})
