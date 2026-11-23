import { describe, expect, it } from 'vitest'
import { formatCompactCurrency, formatCurrency, formatDuration, formatPercent, formatTimeSince } from './format'

describe('formatCurrency', () => {
  it('formats whole dollars with a thousands separator', () => {
    expect(formatCurrency(3000)).toBe('$3,000')
  })

  it('rounds cents to whole dollars', () => {
    expect(formatCurrency(374.6)).toBe('$375')
  })
})

describe('formatTimeSince', () => {
  const now = new Date('2026-11-23T12:00:00Z')
  const minutesAgo = (minutes: number) => new Date(now.getTime() - minutes * 60_000)

  it.each([
    [0, 'just now'],
    [0.5, 'just now'],
    [1, '1 min ago'],
    [59, '59 min ago'],
    [60, '1 hour ago'],
    [150, '2 hours ago'],
    [24 * 60, '1 day ago'],
    [3 * 24 * 60, '3 days ago'],
  ])('%s minutes ago reads "%s"', (minutes, expected) => {
    expect(formatTimeSince(minutesAgo(minutes), now)).toBe(expected)
  })

  it('treats a time slightly in the future as just now', () => {
    expect(formatTimeSince(new Date(now.getTime() + 30_000), now)).toBe('just now')
  })
})

describe('formatCompactCurrency', () => {
  it('keeps amounts under $10,000 exact', () => {
    expect(formatCompactCurrency(8400)).toBe('$8,400')
  })

  it('shortens larger amounts', () => {
    expect(formatCompactCurrency(12_600)).toBe('$12.6K')
    expect(formatCompactCurrency(1_250_000)).toBe('$1.3M')
  })
})

describe('formatPercent', () => {
  it('rounds to a whole percent', () => {
    expect(formatPercent(2 / 3)).toBe('67%')
  })
})

describe('formatDuration', () => {
  const minutes = (count: number) => count * 60_000

  it.each([
    [0.4, 'under 1 min'],
    [45, '45 min'],
    [60, '1 h'],
    [200, '3 h 20 min'],
    [24 * 60, '1 day'],
    [36 * 60, '1.5 days'],
  ])('%s minutes reads "%s"', (count, expected) => {
    expect(formatDuration(minutes(count))).toBe(expected)
  })
})
