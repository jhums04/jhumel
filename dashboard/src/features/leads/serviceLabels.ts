// Mirrors the service keys in the quote form plugin settings (Settings.php).
const serviceLabels: Partial<Record<string, string>> = {
  automation: 'n8n automation workflow',
  lead_system: 'Full lead-to-CRM system',
  supabase_app: 'React + Supabase web app',
  wordpress_plugin: 'Custom WordPress plugin',
  rescue: 'App rescue or security audit',
}

const timelineLabels: Partial<Record<string, string>> = {
  standard: 'Standard',
  rush: 'Rush',
}

// Unknown keys are shown as they are, so a service added in WordPress still appears.
export function getServiceLabel(serviceKey: string): string {
  return serviceLabels[serviceKey] ?? serviceKey
}

export function getTimelineLabel(timeline: string): string {
  return timelineLabels[timeline] ?? timeline
}
