import type { KeyboardCoordinateGetter } from '@dnd-kit/core'
import { isLeadStage, leadStages } from './leadStages'

const columnSteps: Partial<Record<string, number>> = { ArrowLeft: -1, ArrowRight: 1 }

// dnd-kit's default keyboard movement nudges 25px at a time. On a board, left and right should jump a whole column.
export const boardKeyboardCoordinates: KeyboardCoordinateGetter = (event, { context }) => {
  const step = columnSteps[event.code]
  const { over, droppableRects, collisionRect } = context

  if (step === undefined || collisionRect === null || over === null || !isLeadStage(over.id)) {
    return undefined
  }
  event.preventDefault()

  const targetStage = leadStages[leadStages.indexOf(over.id) + step]
  const targetRect = targetStage === undefined ? undefined : droppableRects.get(targetStage)
  if (targetRect === undefined) {
    return undefined
  }

  return {
    x: targetRect.left + (targetRect.width - collisionRect.width) / 2,
    y: collisionRect.top,
  }
}
