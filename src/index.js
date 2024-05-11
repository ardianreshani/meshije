import UserRecipes from './UserRecipes';

import Person from './scripts/Person';
import ExampleReactComponent from './scripts/ExampleReactComponent';
import React from 'react';
import ReactDOM from 'react-dom';

const userRecipe = new userRecipe();
// const person1 = new Person('Brad');
if (document.querySelector('#render-react-example-here')) {
	ReactDOM.render(
		<ExampleReactComponent />,
		document.querySelector('#render-react-example-here')
	);
}

// Create an instance of UserRecipes
const userRecipes = new UserRecipes();