---
title: "Android dev tips and tricks"
date: "2014-08-26"
categories: 
  - "android"
  - "languages"
---

## EditText not updating

### The problem

Say I have fragments for 2 states of a screen: Edit and View. When I switch to the Edit view, I update the editText values to reflect the current data using this code:
```java
@Override
public View onCreateView( LayoutInflater inflater, 
                 ViewGroup container, Bundle savedInstanceState ) {
    super.onCreate( savedInstanceState );

    // inflate and sets the dialog's content view
    View view = inflater.inflate( R.layout.frag_view, null );

    editName = ( EditText ) view.findViewById( R.id.details_name );
    // ...
    updateFields();
}

public void updateFields() {
    // ...
    editName.setText( account.getNameOrDefault() );
    editPseudo.setText( account.getPseudoOrDefault() );
    editEmail.setText( account.getEmailOrDefault() );
    // ...
}
```

When I run it with the debugger, `updateFields` is called and my edit texts have the right values, but on the screen I still get the old ones... DAFUCK ????

### The solution

The EditText appears to have an issue with resetting text in `onCreateView`. So the solution here is to reset the text in `onResume`. This works.

It would also work if called upon `onStart`.

## List onItemLongClick causes stack overflow errors

### The problem

I have a list adapter and want two actions:

1. one regular click opens a detailed view
2. one long click opens a context menu

The regular click works, but the long click produces stack overflows...

The codes:
```java
 registerForContextMenu( mList );

// one click on an item opens the "detail" view of the account entry
mList.setOnItemClickListener( 
    new AdapterView.OnItemClickListener(){
        @Override
        public void onItemClick( AdapterView parent, 
                   View v, int pos, long id ){
            // ... do stuff ...
        }
} );

mList.setOnItemLongClickListener( 
    new AdapterView.OnItemLongClickListener(){
        @Override
        public boolean onItemLongClick( AdapterView parent,
                   View v, int pos, long id ){
            mList.showContextMenuForChild( view );
            return true;
        }
} );
```

### The solution

We have registered the ListView for a ContextMenu and set an OnItemLongClickListener that calls `showContextMenu()`, this creates a circular logic:

- The `OnItemLongClickListener` calls the `ContextMenu`,
- The `ContextMenu` calls the `OnItemLongClickListener`,
- The `OnItemLongClickListener` calls the `ContextMenu`,
- The `ContextMenu` calls the `OnItemLongClickListener`,

etc. until the stack overflow occurs To fix this: remove the OnItemLongClickListener since it is redundant with a ContextMenu. In fact, the `registerForContextMenu` does exactly that!
