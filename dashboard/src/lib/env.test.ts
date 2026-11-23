import { describe, expect, it } from 'vitest'
import { readEnv } from './env'

const supabaseSettings = {
  VITE_SUPABASE_URL: 'http://127.0.0.1:54321',
  VITE_SUPABASE_PUBLISHABLE_KEY: 'sb_publishable_test',
  VITE_N8N_FOLLOW_UP_URL: 'http://localhost:5678/webhook/send-follow-up',
}

describe('readEnv', () => {
  it('returns the Supabase and n8n settings when all are set', () => {
    const env = readEnv(supabaseSettings)

    expect(env.supabaseUrl).toBe('http://127.0.0.1:54321')
    expect(env.supabasePublishableKey).toBe('sb_publishable_test')
    expect(env.followUpUrl).toBe('http://localhost:5678/webhook/send-follow-up')
  })

  it('names every missing variable', () => {
    expect(() => readEnv({})).toThrow(
      'Missing environment variables: VITE_SUPABASE_URL, VITE_SUPABASE_PUBLISHABLE_KEY, VITE_N8N_FOLLOW_UP_URL.',
    )
  })

  it('treats a blank value as missing', () => {
    expect(() =>
      readEnv({ ...supabaseSettings, VITE_SUPABASE_URL: '  ' }),
    ).toThrow('Missing environment variables: VITE_SUPABASE_URL.')
  })

  it('returns the demo login when both demo variables are set', () => {
    const env = readEnv({
      ...supabaseSettings,
      VITE_DEMO_EMAIL: 'demo@jhumel.dev',
      VITE_DEMO_PASSWORD: 'demo-password',
    })

    expect(env.demoLogin).toEqual({ email: 'demo@jhumel.dev', password: 'demo-password' })
  })

  it('leaves the demo login out when either demo variable is missing', () => {
    expect(readEnv(supabaseSettings).demoLogin).toBeNull()
    expect(readEnv({ ...supabaseSettings, VITE_DEMO_EMAIL: 'demo@jhumel.dev' }).demoLogin).toBeNull()
  })
})
