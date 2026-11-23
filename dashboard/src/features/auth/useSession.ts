import type { Session } from '@supabase/supabase-js'
import { useEffect, useState } from 'react'
import { useOutletContext } from 'react-router'
import { supabase } from '../../lib/supabase'

export type SessionState =
  | { status: 'loading' }
  | { status: 'signed-in'; session: Session }
  | { status: 'signed-out' }

// Supabase reports the stored session first (INITIAL_SESSION), then every sign-in, sign-out, and token refresh.
export function useSession(): SessionState {
  const [sessionState, setSessionState] = useState<SessionState>({ status: 'loading' })

  useEffect(() => {
    const { data } = supabase.auth.onAuthStateChange((_event, session) => {
      setSessionState(session === null ? { status: 'signed-out' } : { status: 'signed-in', session })
    })

    return () => {
      data.subscription.unsubscribe()
    }
  }, [])

  return sessionState
}

// For pages inside RequireAuth, which passes the session down through the route outlet.
export function useSignedInSession(): Session {
  return useOutletContext<Session>()
}
