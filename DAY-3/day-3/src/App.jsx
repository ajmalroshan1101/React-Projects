// Important:
// For this project to work on CodeSandbox, image assets ("assets") folder
// must be stored in the public folder (as it's the case by default in this project)

// here you import the images or assest in this manner in React 
import reactImage from './assets/react-core-concepts.png'
import ComponentImage from  './assets/components.png'
import ConfigImg from  './assets/config.png'
import JsxImg from  './assets/jsx-ui.png'

import Header from './components/Header'

import CoreConcept from './components/CoreConcept'
import { CORE_CONCEPTS } from './data'
//this example for dynamuic content 

function genRandomInt(max) {
  return Math.floor(Math.random() * (max + 1));
}





function App() {
  return (
    <div>
      {/* Function name Spelling must Be correct */}
      {/* this is one way of calling the custom component in App Component */}
      <Header></Header>

      {/* below is the second way of calling the custome Header 
      Component in App () here always remember to put the '/' */}

      {/* <Header/> */} 
      <main>
        <h2>Time to get started!</h2>

        {/* Below code is the example for core concept  */}
        {/* Here the App is the parent Component and the parent Component passing the data to the child Component */}
        <section id="core-concepts">
          <h2>Core Concept</h2>

            <ul>
              {/* Here CoreConcept is a child Component Here we are Passing the Object to the Child Component */}
              {/* Here 4 time the child component is called so time it will show in the DOM  */}
              {/* this is another method of passing the data to the parent page by importing the values from another file and assining to variables */}
                {/* <CoreConcept title={CORE_CONCEPTS[0].title} discription={CORE_CONCEPTS[0].description} img={CORE_CONCEPTS[0].image}/> */}

             {/* this is the shorter way of above code using the spread operator the key value pair is made to  one object */}
             {/* also want to check the spelling of the key in the object correct */}
                <CoreConcept {...CORE_CONCEPTS[0]}/>


                <CoreConcept title={CORE_CONCEPTS[1].title} description={CORE_CONCEPTS[1].description} image={CORE_CONCEPTS[1].image}/>
                <CoreConcept title={CORE_CONCEPTS[2].title} description={CORE_CONCEPTS[2].description} image={CORE_CONCEPTS[2].image}/>
                <CoreConcept title={CORE_CONCEPTS[3].title} description={CORE_CONCEPTS[3].description} image={CORE_CONCEPTS[3].image}/>
            </ul>
        </section>
      </main>
    </div>
  );
}

export default App;
