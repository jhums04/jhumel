import { KeyboardSensor, MouseSensor, TouchSensor, useSensor, useSensors } from '@dnd-kit/core'
import { boardKeyboardCoordinates } from './boardKeyboardCoordinates'

// How cards start dragging. Mouse drags start after 5px so a click opens the lead; touch waits 250ms so the
// board still scrolls; the keyboard uses Space only, because Enter opens the lead.
export function useBoardSensors() {
  return useSensors(
    useSensor(MouseSensor, { activationConstraint: { distance: 5 } }),
    useSensor(TouchSensor, { activationConstraint: { delay: 250, tolerance: 5 } }),
    useSensor(KeyboardSensor, {
      coordinateGetter: boardKeyboardCoordinates,
      keyboardCodes: { start: ['Space'], cancel: ['Escape'], end: ['Space'] },
    }),
  )
}
