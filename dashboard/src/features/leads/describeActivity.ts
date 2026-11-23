import type { LeadActivity } from './leadsApi'
import { getStageLabel, isLeadStage } from './leadStages'

function readText(details: LeadActivity['details'], key: string): string | null {
  if (typeof details !== 'object' || Array.isArray(details)) {
    return null
  }
  const value = details[key]
  return typeof value === 'string' && value.trim() !== '' ? value : null
}

// Activity details are free-form JSON written by the database trigger and by n8n, so every field is checked.
export function describeActivity(activity: LeadActivity): string {
  const { type, details } = activity

  if (type === 'stage_changed') {
    const from = readText(details, 'from')
    const to = readText(details, 'to')
    return isLeadStage(from) && isLeadStage(to)
      ? `Moved from ${getStageLabel(from)} to ${getStageLabel(to)}`
      : 'Stage changed'
  }

  if (type === 'email_sent') {
    const subject = readText(details, 'subject')
    const isDemo = typeof details === 'object' && !Array.isArray(details) && details.delivered === false
    const action = isDemo ? 'Reply logged (demo, not delivered)' : 'Reply sent'
    return subject === null ? action : `${action}: ${subject}`
  }

  if (type === 'note') {
    const text = readText(details, 'text')
    return text === null ? 'Note added' : `Note: ${text}`
  }

  return 'Activity recorded'
}
