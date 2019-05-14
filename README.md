# Simple todolist

A simple todolist build with ReactJS and Framework7 frontend frameworks and a home made PHP API framework.
This example implements CRUD with ReactJS, Redux and Redux-Saga.

## Getting Started

Before starting, ensure to have [docker](https://docs.docker.com/install/) and [npm](https://www.npmjs.com/get-npm) installed. If not, it's time to.

### Prerequisites

Clone the project then enter into.

```
git clone https://github/ilsduc/todolist.git
cd todolist
```

### Demo

See the live demo [application](https://todolist.ilsduc.fr/) and the [api documentation](https://api.todolist.ilsduc.fr/documentation)

### Dependencies

Run the following command in root folder

```
npm run init-project
```

### Run

Then run

```
npm run start
```

Or run the two parts seperatly
* API server
```
cd api && docker-compose up -d
```

* Development express server
```
cd app && npm run start
```
### Create the database

Go to ```http://localhost:4000/documentation``` and click "create schema" button.

API is reacheable under http://localhost:4000 and the application under http://localhost:8080
