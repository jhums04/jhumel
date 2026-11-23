import { DndContext } from '@dnd-kit/core'
import { render, screen } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { MemoryRouter, Route, Routes, useLocation } from 'react-router'
import { describe, expect, it } from 'vitest'
import { createTestLead } from '../../test/createTestLead'
import { LeadCard } from './LeadCard'
import { useBoardSensors } from './useBoardSensors'

function DrawerStandIn() {
  const location = useLocation()
  return <p>Drawer at {`${location.pathname}${location.search}`}</p>
}

function BoardStandIn() {
  return (
    <DndContext sensors={useBoardSensors()}>
      <ul>
        <LeadCard lead={createTestLead({ id: 'lead-1' })} currentTime={new Date('2026-11-23T12:00:00Z')} />
      </ul>
    </DndContext>
  )
}

function renderCard() {
  render(
    <MemoryRouter initialEntries={['/?organization=demo']}>
      <Routes>
        <Route
          path="/"
element={<BoardStandIn />} />
        <Route path="/leads/:leadId" element={<DrawerStandIn />} />
      </Routes>
    </MemoryRouter>,
  )
}

describe('LeadCard', () => {
  it('opens the lead when clicked, keeping the selected organization', async () => {
    renderCard()

    await userEvent.click(screen.getByRole('button', { name: /Maria Santos/ }))

    expect(screen.getByText('Drawer at /leads/lead-1?organization=demo')).toBeInTheDocument()
  })

  it('opens the lead with the Enter key', async () => {
    renderCard()

    screen.getByRole('button', { name: /Maria Santos/ }).focus()
    await userEvent.keyboard('{Enter}')

    expect(screen.getByText('Drawer at /leads/lead-1?organization=demo')).toBeInTheDocument()
  })
})
