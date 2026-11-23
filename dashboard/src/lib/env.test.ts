import { describe, expect, it } from 'vitest'
import { readEnv } from './env'

describe('readEnv', () => {
  it('returns the Supabase settings when both are set', () => {
    const env = readEnv({
      VITE_SUPABASE_URL: 'http://127.0.0.1:54321',
      VITE_SUPABASE_PUBLISHABLE_KEY: 'sb_publishable_test',
    })

    expect(env).toEqual({
      supabaseUrl: 'http://127.0.0.1:54321',
      supabasePublishableKey: 'sb_publishable_test',
    })
  })

  it('names every missing variable', () => {
    expect(() => readEnv({})).toThrow(
      'Missing environment variables: VITE_SUPABASE_URL, VITE_SUPABASE_PUBLISHABLE_KEY.',
    )
  })

  it('treats a blank value as missing', () => {
    expect(() =>
      readEnv({ VITE_SUPABASE_URL: '  ', VITE_SUPABASE_PUBLISHABLE_KEY: 'sb_publishable_test' }),
    ).toThrow('Missing environment variables: VITE_SUPABASE_URL.')
  })
})
