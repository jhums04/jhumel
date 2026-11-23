import { isAuthApiError, isAuthRetryableFetchError } from '@supabase/supabase-js'
import { useMutation } from '@tanstack/react-query'
import { useState, type FormEvent } from 'react'
import { Navigate, useLocation } from 'react-router'
import { Button } from '../../components/Button'
import { env } from '../../lib/env'
import { supabase } from '../../lib/supabase'
import { useSession } from './useSession'

type Credentials = {
  email: string
  password: string
}

// RequireAuth stores where the visitor was going. Only same-site paths are followed.
function readRedirectPath(locationState: unknown): string {
  if (
    typeof locationState === 'object' &&
    locationState !== null &&
    'from' in locationState &&
    typeof locationState.from === 'string' &&
    locationState.from.startsWith('/') &&
    !locationState.from.startsWith('//')
  ) {
    return locationState.from
  }
  return '/'
}

function describeSignInError(error: Error): string {
  if (isAuthApiError(error) && error.code === 'invalid_credentials') {
    return 'Email or password is incorrect.'
  }
  if (isAuthRetryableFetchError(error)) {
    return 'Could not reach the server. Check your connection and try again.'
  }
  return 'Sign-in failed. Try again in a moment.'
}

async function signInWithPassword(credentials: Credentials): Promise<void> {
  const { error } = await supabase.auth.signInWithPassword(credentials)
  if (error) {
    throw error
  }
}

const inputClassName =
  'mt-2 block w-full rounded-lg border border-gray-200 bg-white px-3 py-3 text-black focus:border-navy'

export function LoginPage() {
  const sessionState = useSession()
  const location = useLocation()
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const redirectPath = readRedirectPath(location.state)

  // No navigation on success: Supabase reports the new session, and the signed-in check below redirects.
  const signIn = useMutation({ mutationFn: signInWithPassword })

  if (sessionState.status === 'loading') {
    return null
  }

  if (sessionState.status === 'signed-in') {
    return <Navigate to={redirectPath} replace />
  }

  function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault()
    signIn.mutate({ email, password })
  }

  function fillDemoLogin() {
    if (env.demoLogin === null) {
      return
    }
    setEmail(env.demoLogin.email)
    setPassword(env.demoLogin.password)
  }

  return (
    <main className="flex min-h-screen items-center justify-center px-4 py-16">
      <div className="w-full max-w-sm">
        <h1 className="text-2xl font-bold">Sign in to Lead Engine</h1>

        <form onSubmit={handleSubmit} className="mt-8 space-y-5">
          {signIn.isError && (
            <p role="alert" className="rounded-lg border border-error bg-error-background px-4 py-3 text-sm text-error">
              {describeSignInError(signIn.error)}
            </p>
          )}

          <div>
            <label htmlFor="email" className="block text-sm font-semibold">
              Email
            </label>
            <input
              id="email"
              type="email"
              autoComplete="email"
              required
              value={email}
              onChange={(event) => setEmail(event.target.value)}
              className={inputClassName}
            />
          </div>

          <div>
            <label htmlFor="password" className="block text-sm font-semibold">
              Password
            </label>
            <input
              id="password"
              type="password"
              autoComplete="current-password"
              required
              value={password}
              onChange={(event) => setPassword(event.target.value)}
              className={inputClassName}
            />
          </div>

          <Button type="submit" disabled={signIn.isPending} className="w-full">
            {signIn.isPending ? 'Signing in...' : 'Sign in'}
          </Button>
        </form>

        {env.demoLogin !== null && (
          <section aria-labelledby="demo-login-heading" className="mt-8 border-t border-gray-200 pt-6">
            <h2 id="demo-login-heading" className="text-sm font-semibold">
              Exploring the demo?
            </h2>
            <p className="mt-1 text-sm text-gray-600">
              Sign in as <span className="font-mono">{env.demoLogin.email}</span> to see sample leads.
            </p>
            <Button variant="secondary" onClick={fillDemoLogin} className="mt-3">
              Fill in demo login
            </Button>
          </section>
        )}
      </div>
    </main>
  )
}
