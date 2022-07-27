---
title: "Never skrew up a binary search again"
date: "2014-01-17"
categories: 
  - "languages"
  - "other"
---

I you are like me, it is a pain in the \*\*\* to write an effective binary search: is the condition of the loop a low < high or a low <= high ? Where to put the + 1 and the - 1 ?

Here is a simple way of remembering the algorithm.

To sort an array, we use to pointer: one, `low`, moves to the upper indexes of the array and the other, `high`, begins at the end and goes from right to left (moves to the lower indexes).

We pose two constraints:
```python
array[ low ]  <  X
array[ high ] >= X
```

whatever the values of `low` and `high`.

At the beginning, `low` points to a phantom case located before the first case and `high` points to the case after the last case of the array:
```python
BEGIN:
low = -1
high = array.length
```

We then want to move the two pointers towards each other until they point to contiguous cases, namely `low + 1 == high` or `low == high - 1`. At this point, we have searched the whole array; if the element to find exists, it will be in either `array[ low ]` or `array[ high ]`, depending on the operators we used (see below).

Finally, the code is rather simple:
```java
int low = -1;
int high = array.length;

while( low + 1 < high ){
  int middle = low + (low + high ) / 2;

  if( array[ middle ] < X ) // (1)
    low = middle;
  else
    high = middle;
}

if( low < high && array[ high ] == X ) // (2)
  return X;

return -1;
```

One of the advantages of this method is that there is no +1 or -1 anymore. Moreover, we can choose which element we return in case it appears multiple times in the array. We just need to recall our constraint! By stating that all elements equals are on the right, we will return the first match; if we change the constraint and put all the elements equals to the left, at the end `array[ low ]` will return the last match...

So to resume, to return the first match:
```java
if( array[ middle ] < low )
  return array[ high ]
```

to return the last match:
```java
if( array[ middle ] <= low )
  return array[ low ]
```