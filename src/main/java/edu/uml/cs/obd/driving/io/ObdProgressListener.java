package edu.uml.cs.obd.driving.io;

public interface ObdProgressListener {

    void stateUpdate(final ObdCommandJob job);

}