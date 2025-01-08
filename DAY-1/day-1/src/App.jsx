// Important:
// For this project to work on CodeSandbox, image assets ("assets") folder
// must be stored in the public folder (as it's the case by default in this project)

// here you import the images or assest in this manner in React 
import reactImage from './assets/react-core-concepts.png'
import ComponentImage from  './assets/components.png'
import ConfigImg from  './assets/config.png'
import JsxImg from  './assets/jsx-ui.png'
//this example for dynamuic content 
const reactDescriptions = ['Fundamental', 'Crucial', 'Core'];

function genRandomInt(max) {
  return Math.floor(Math.random() * (max + 1));
}

//this is example for the custome component 
function Header(){

  return (
    <header>
      {/* insteded of giving the image path use the imported character in the scr and don't use "" */}
        <img src={reactImage} alt="Stylized atom" />
        <h1>React Essentials</h1>
        <p>
          {/* here is the example the dynamic content  */}
          {reactDescriptions[genRandomInt(2)]} React concepts you will need for almost any app you are
          going to build!
        </p>
      </header>

  )
}

// This the Child component the data passed from the parent component is used in the component
function CoreConcept(props){
  // Props is used access the data from the parent component 
  // Is Component is Dynamic 
  return (
  <li>
    <img src={props.img} alt="" />
    <h3>
      {props.title}
    </h3>
    <p>
       {props.discription}
    </p>
  </li>
  )
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
                <CoreConcept title="Components" discription="the component is working" img={ComponentImage}/>
                <CoreConcept title="Props"      discription="the Props is working" img={ConfigImg}/>
                <CoreConcept title="Core"       discription="the Core is working" img={JsxImg}/>
                <CoreConcept title="Child "     discription="the Child is working" img={reactImage}/>
            </ul>
        </section>
      </main>
    </div>
  );
}

export default App;
