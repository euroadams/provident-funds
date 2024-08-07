button class="c-hamburger c-hamburger--rot">
  <span>toggle menu</span>
</button>

<button class="c-hamburger c-hamburger--htx">
  <span>toggle menu</span>
</button>

<button class="c-hamburger c-hamburger--htla">
  <span>toggle menu</span>
</button>

<button class="c-hamburger c-hamburger--htra">
  <span>toggle menu</span>
</button>


.c-hamburger {
  display: block;
  position: relative;
  overflow: hidden;
  margin: 0;
  padding: 0;
  width: 96px;
  height: 96px;
  font-size: 0;
  text-indent: -9999px;
  appearance: none;
  box-shadow: none;
  border-radius: none;
  border: none;
  cursor: pointer;
  transition: background 0.3s;
}

.c-hamburger:focus {
  outline: none;
}



.c-hamburger span {
  display: block;
  position: absolute;
  top: 44px;
  left: 18px;
  right: 18px;
  height: 8px;
  background: white;
}

.c-hamburger span::before,
.c-hamburger span::after {
  position: absolute;
  display: block;
  left: 0;
  width: 100%;
  height: 8px;
  background-color: #fff;
  content: "";
}

.c-hamburger span::before {
  top: -20px;
}

.c-hamburger span::after {
  bottom: -20px;
}


.c-hamburger--htx {
  background-color: #ff3264;
}

.c-hamburger--htx span {
  transition: background 0s 0.3s;
}

.c-hamburger--htx span::before,
.c-hamburger--htx span::after {
  transition-duration: 0.3s, 0.3s;
  transition-delay: 0.3s, 0s;
}

.c-hamburger--htx span::before {
  transition-property: top, transform;
}

.c-hamburger--htx span::after {
  transition-property: bottom, transform;
}

/* active state, i.e. menu open */
.c-hamburger--htx.is-active {
  background-color: #cb0032;
}

.c-hamburger--htx.is-active span {
  background: none;
}

.c-hamburger--htx.is-active span::before {
  top: 0;
  transform: rotate(45deg);
}

.c-hamburger--htx.is-active span::after {
  bottom: 0;
  transform: rotate(-45deg);
}

.c-hamburger--htx.is-active span::before,
.c-hamburger--htx.is-active span::after {
  transition-delay: 0s, 0.3s;
}


