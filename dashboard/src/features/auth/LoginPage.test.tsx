import { AuthApiError, type AuthChangeEvent, type Session, type SupabaseClient } from '@supabase/supabase-js'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { render, screen } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { MemoryRouter, Route, Routes } from 'react-router'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import { createTestSession } from '../../test/createTestSession'
import { LoginPage } from './LoginPage'

type AuthListener = (event: AuthChangeEvent, session: Session | null) => void
type SignInWithPassword = SupabaseClient['auth']['signInWithPassword']

const authMocks = vi.hoisted(() => ({
  onAuthStateChange: vi.fn<(listener: AuthListener) => { data: { subscription: { unsubscribe: () => void } } }>(),
  signInWithPassword: vi.fn<SignInWithPassword>(),
}))

vi.mock('../../lib/env', () => ({
  env: { demoLogin: { email: 'demo@jhumel.dev', password: 'demo-password' } },
}))

vi.mock('../../lib/supabase', () => ({ supabase: { auth: authMocks } }))

// Lets a test announce a sign-in the way Supabase does.
let notifyAuthListener: AuthListener = () => undefined

beforeEach(() => {
  authMocks.onAuthStateChange.mockImplementation((listener) => {
    notifyAuthListener = listener
    listener('INITIAL_SESSION', null)
    return { data: { subscription: { unsubscribe: () => undefined } } }
  })
})

function renderLoginPage() {
  render(
    <QueryClientProvider client={new QueryClient()}>
      <MemoryRouter initialEntries={[{ pathname: '/login', state: { from: '/leads' } }]}>
        <Routes>
          <Route path="/login" element={<LoginPage />} />
          <Route path="/leads" element={<p>Leads page</p>} />
        </Routes>
      </MemoryRouter>
    </QueryClientProvider>,
  )
}

async function signIn(email: string, password: string) {
  await userEvent.type(screen.getByLabelText('Email'), email)
  await userEvent.type(screen.getByLabelText('Password'), password)
  await userEvent.click(screen.getByRole('button', { name: 'Sign in' }))
}

describe('LoginPage', () => {
  it('shows a plain message when the password is wrong', async () => {
    authMocks.signInWithPassword.mockResolvedValue({
      data: { user: null, session: null },
      error: new AuthApiError('Invalid login credentials', 400, 'invalid_credentials'),
    })
    renderLoginPage()

    await signIn('demo@jhumel.dev', 'wrong-password')

    expect(await screen.findByRole('alert')).toHaveTextContent('Email or password is incorrect.')
  })

  it('returns to the page the visitor was going to after signing in', async () => {
    authMocks.signInWithPassword.mockImplementation(() => {
      const session = createTestSession('demo@jhumel.dev')
      notifyAuthListener('SIGNED_IN', session)
      return Promise.resolve({ data: { user: session.user, session }, error: null })
    })
    renderLoginPage()

    await signIn('demo@jhumel.dev', 'demo-password')

    expect(await screen.findByText('Leads page')).toBeInTheDocument()
    expect(authMocks.signInWithPassword).toHaveBeenCalledWith({
      email: 'demo@jhumel.dev',
      password: 'demo-password',
    })
  })

  it('fills in the demo login', async () => {
    renderLoginPage()

    await userEvent.click(screen.getByRole('button', { name: 'Fill in demo login' }))

    expect(screen.getByLabelText('Email')).toHaveValue('demo@jhumel.dev')
    expect(screen.getByLabelText('Password')).toHaveValue('demo-password')
  })
})
