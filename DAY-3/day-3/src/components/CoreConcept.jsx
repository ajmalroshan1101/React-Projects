

// This the Child component the data passed from the parent component is used in the component
export default function CoreConcept(props){
  // Props is used access the data from the parent component 
  // Is Component is Dynamic 
  return (
  <li>
    <img src={props.image} alt="" />
    <h3>
      {props.title}
    </h3>
    <p>
       {props.description}

    </p>
  </li>
  )
}