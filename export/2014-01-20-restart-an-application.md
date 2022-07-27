---
title: "Restart an application"
date: "2014-01-20"
categories: 
  - "android"
  - "languages"
---

Sometimes, it is easier to start anew than to update the state of your activities. In my case, I used that after importing a db file from backup storage. Instead of reinitialising all the db instances, I just restarted the app... This can be considered ugly but at least it works.

Here is the code I used:

```java
// get a reference to the current activity context or,
// in my case, from the application
Context context = PipiApplication.getContext();

// create a new instance to start the main activity
Intent restartIntent = new Intent( context, MainActivity.class );
restartIntent.setFlags(
        Intent.FLAG_ACTIVITY_NEW_TASK |
        Intent.FLAG_ACTIVITY_NO_HISTORY
    );

// a pendingIntent grants another application the privilege to
// start your activity as if it was your app
int pendingIntentID = 123456;
PendingIntent pendingIntent = PendingIntent.getActivity(
        context,
        pendingIntentID,
        restartIntent,
        PendingIntent.FLAG_CANCEL_CURRENT
    );

// ask the alarmManager to launch your intent after a short delay
AlarmManager mgr = ( AlarmManager )getBaseContext().getSystemService(
        Context.ALARM_SERVICE
    );

mgr.set( AlarmManager.RTC,
         System.currentTimeMillis() + 100,
         pendingIntent
    );

// exit the main activity smoothly
// Note: if you are not in the main activity, you should
// try System.exit(0) instead
finish();
```
