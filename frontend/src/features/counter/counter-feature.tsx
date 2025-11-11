import { CounterCard } from './components/counter-card'
import { useCounter } from './hooks/use-counter'

export const CounterFeature = () => {
  const { count, increment } = useCounter()

  return <CounterCard count={count} onIncrement={increment} />
}
