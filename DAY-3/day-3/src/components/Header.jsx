import reactImage from '../assets/react-core-concepts.png'

const reactDescriptions = ['Fundamental', 'Crucial', 'Core'];


function genRandomInt(max) {
  return Math.floor(Math.random() * (max + 1));
}

//this is example for the custome component 
//Here i Created a new component for arranging the react project 

//must want to export the Function
export default function Header(){

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