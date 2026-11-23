import { describe, expect, it } from 'vitest'
import { createTestLead } from '../../test/createTestLead'
import { calculatePipelineMetrics } from './calculatePipelineMetrics'

const startOfToday = new Date('2026-11-23T00:00:00+08:00')

describe('calculatePipelineMetrics', () => {
  describe('leads today', () => {
    it('counts leads from midnight on, not the evening before', () => {
      const leads = [
        createTestLead({ id: 'yesterday', created_at: '2026-11-22T23:59:00+08:00' }),
        createTestLead({ id: 'midnight', created_at: '2026-11-23T00:00:00+08:00' }),
        createTestLead({ id: 'morning', created_at: '2026-11-23T09:30:00+08:00' }),
      ]

      const metrics = calculatePipelineMetrics(leads, [], startOfToday)

      expect(metrics.leadsToday).toBe(2)
      expect(metrics.totalLeads).toBe(3)
    })
  })

  describe('open pipeline value', () => {
    it('adds up estimates of leads that are not won or lost', () => {
      const leads = [
        createTestLead({ id: 'new', stage: 'new', estimated_price: 2000 }),
        createTestLead({ id: 'proposal', stage: 'proposal_sent', estimated_price: 1500 }),
        createTestLead({ id: 'won', stage: 'won', estimated_price: 9000 }),
        createTestLead({ id: 'lost', stage: 'lost', estimated_price: 9000 }),
      ]

      const metrics = calculatePipelineMetrics(leads, [], startOfToday)

      expect(metrics.openPipelineValue).toBe(3500)
      expect(metrics.openLeads).toBe(2)
    })

    it('treats a lead without an estimate as zero', () => {
      const leads = [
        createTestLead({ id: 'priced', estimated_price: 600 }),
        createTestLead({ id: 'unpriced', estimated_price: null }),
      ]

      expect(calculatePipelineMetrics(leads, [], startOfToday).openPipelineValue).toBe(600)
    })
  })

  describe('conversion rate', () => {
    it('divides won leads by all closed leads', () => {
      const leads = [
        createTestLead({ id: 'won-1', stage: 'won' }),
        createTestLead({ id: 'won-2', stage: 'won' }),
        createTestLead({ id: 'lost', stage: 'lost' }),
        createTestLead({ id: 'open', stage: 'contacted' }),
      ]

      const metrics = calculatePipelineMetrics(leads, [], startOfToday)

      expect(metrics.conversionRate).toBeCloseTo(2 / 3)
      expect(metrics.wonLeads).toBe(2)
      expect(metrics.lostLeads).toBe(1)
    })

    it('has no rate until a lead is won or lost', () => {
      const leads = [createTestLead({ stage: 'new' })]

      expect(calculatePipelineMetrics(leads, [], startOfToday).conversionRate).toBeNull()
    })
  })

  describe('average response time', () => {
    it('averages the time from arrival to the first reply', () => {
      const leads = [
        createTestLead({ id: 'fast', created_at: '2026-11-23T10:00:00Z' }),
        createTestLead({ id: 'slow', created_at: '2026-11-23T10:00:00Z' }),
      ]
      const firstReplies = [
        { leadId: 'fast', repliedAt: '2026-11-23T10:30:00Z' },
        { leadId: 'slow', repliedAt: '2026-11-23T11:30:00Z' },
      ]

      const metrics = calculatePipelineMetrics(leads, firstReplies, startOfToday)

      expect(metrics.averageResponseMilliseconds).toBe(60 * 60_000)
      expect(metrics.repliedLeads).toBe(2)
    })

    it('leaves out leads without a reply and replies for leads not on the board', () => {
      const leads = [
        createTestLead({ id: 'replied', created_at: '2026-11-23T10:00:00Z' }),
        createTestLead({ id: 'waiting', created_at: '2026-11-23T10:00:00Z' }),
      ]
      const firstReplies = [
        { leadId: 'replied', repliedAt: '2026-11-23T10:20:00Z' },
        { leadId: 'other-organization', repliedAt: '2026-11-23T10:01:00Z' },
      ]

      const metrics = calculatePipelineMetrics(leads, firstReplies, startOfToday)

      expect(metrics.averageResponseMilliseconds).toBe(20 * 60_000)
      expect(metrics.repliedLeads).toBe(1)
    })

    it('has no average before the first reply', () => {
      expect(calculatePipelineMetrics([createTestLead()], [], startOfToday).averageResponseMilliseconds).toBeNull()
    })
  })
})
