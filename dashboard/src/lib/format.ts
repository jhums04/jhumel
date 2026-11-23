const currencyFormatter = new Intl.NumberFormat('en-US', {
  style: 'currency',
  currency: 'USD',
  maximumFractionDigits: 0,
})

export function formatCurrency(amount: number): string {
  return currencyFormatter.format(amount)
}

function pluralize(count: number, unit: string): string {
  return `${count} ${unit}${count === 1 ? '' : 's'} ago`
}

export function formatTimeSince(since: Date, now: Date): string {
  const minutes = Math.floor((now.getTime() - since.getTime()) / 60_000)
  if (minutes < 1) {
    return 'just now'
  }
  if (minutes < 60) {
    return `${minutes} min ago`
  }

  const hours = Math.floor(minutes / 60)
  if (hours < 24) {
    return pluralize(hours, 'hour')
  }

  return pluralize(Math.floor(hours / 24), 'day')
}

const compactCurrencyFormatter = new Intl.NumberFormat('en-US', {
  style: 'currency',
  currency: 'USD',
  notation: 'compact',
  maximumFractionDigits: 1,
})

// For headline figures: $8,400 stays exact, $12,600 becomes $12.6K.
export function formatCompactCurrency(amount: number): string {
  return amount < 10_000 ? formatCurrency(amount) : compactCurrencyFormatter.format(amount)
}

export function formatPercent(ratio: number): string {
  return `${Math.round(ratio * 100)}%`
}

export function formatDuration(milliseconds: number): string {
  const totalMinutes = Math.round(milliseconds / 60_000)
  if (totalMinutes < 1) {
    return 'under 1 min'
  }
  if (totalMinutes < 60) {
    return `${totalMinutes} min`
  }

  const totalHours = Math.floor(totalMinutes / 60)
  if (totalHours < 24) {
    const minutes = totalMinutes % 60
    return minutes === 0 ? `${totalHours} h` : `${totalHours} h ${minutes} min`
  }

  const days = Math.round((totalHours / 24) * 10) / 10
  return `${days} ${days === 1 ? 'day' : 'days'}`
}
