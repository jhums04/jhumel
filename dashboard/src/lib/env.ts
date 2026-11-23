type DemoLogin = {
  email: string
  password: string
}

type Env = {
  supabaseUrl: string
  supabasePublishableKey: string
  followUpUrl: string
  // Shown on the login page so visitors can try the demo. Null when not configured.
  demoLogin: DemoLogin | null
}

type EnvSource = Record<string, string | boolean | undefined>

function readText(source: EnvSource, name: string): string {
  const value = source[name]
  return typeof value === 'string' ? value.trim() : ''
}

// Fails at startup with the missing names, instead of later as a confusing network error.
export function readEnv(source: EnvSource): Env {
  const requiredNames = ['VITE_SUPABASE_URL', 'VITE_SUPABASE_PUBLISHABLE_KEY', 'VITE_N8N_FOLLOW_UP_URL']
  const missingNames = requiredNames.filter((name) => readText(source, name) === '')

  if (missingNames.length > 0) {
    throw new Error(
      `Missing environment variables: ${missingNames.join(', ')}. Copy .env.example to .env and fill them in.`,
    )
  }

  const demoEmail = readText(source, 'VITE_DEMO_EMAIL')
  const demoPassword = readText(source, 'VITE_DEMO_PASSWORD')

  return {
    supabaseUrl: readText(source, 'VITE_SUPABASE_URL'),
    supabasePublishableKey: readText(source, 'VITE_SUPABASE_PUBLISHABLE_KEY'),
    followUpUrl: readText(source, 'VITE_N8N_FOLLOW_UP_URL'),
    demoLogin: demoEmail !== '' && demoPassword !== '' ? { email: demoEmail, password: demoPassword } : null,
  }
}

export const env = readEnv(import.meta.env)
