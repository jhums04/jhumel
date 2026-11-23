type Env = {
  supabaseUrl: string
  supabasePublishableKey: string
}

type EnvSource = Record<string, string | boolean | undefined>

// Fails at startup with the missing names, instead of later as a confusing network error.
export function readEnv(source: EnvSource): Env {
  const requiredNames = ['VITE_SUPABASE_URL', 'VITE_SUPABASE_PUBLISHABLE_KEY']
  const missingNames = requiredNames.filter((name) => {
    const value = source[name]
    return typeof value !== 'string' || value.trim() === ''
  })

  if (missingNames.length > 0) {
    throw new Error(
      `Missing environment variables: ${missingNames.join(', ')}. Copy .env.example to .env and fill them in.`,
    )
  }

  return {
    supabaseUrl: String(source.VITE_SUPABASE_URL),
    supabasePublishableKey: String(source.VITE_SUPABASE_PUBLISHABLE_KEY),
  }
}

export const env = readEnv(import.meta.env)
